<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{

    private function sanitizeForJson($data)
{
    if (is_string($data)) {
        $sanitized = @iconv('UTF-8', 'UTF-8//IGNORE', $data);
        if ($sanitized === false) {
            $sanitized = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
        }
        return $sanitized;
    }
    if ($data instanceof \Illuminate\Database\Eloquent\Model) {
        return $this->sanitizeForJson($data->toArray());
    }
    if ($data instanceof \Illuminate\Support\Collection) {
        return $data->map(fn($item) => $this->sanitizeForJson($item))->all();
    }
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = $this->sanitizeForJson($value);
        }
        return $data;
    }
    return $data;
}
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $reservations = Reservation::orderBy('created_at', 'desc')->get();
            $sanitized = $this->sanitizeForJson($reservations);
return response()->json($sanitized, 200, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch reservations'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Log initial payload and attempt JSON parsing fallback
            $rawContent = $request->getContent();
            $initialParsed = $request->all();
            \Log::info('Incoming reservation payload', [
                'raw' => $rawContent,
                'parsed' => $initialParsed,
                'headers' => $request->headers->all(),
            ]);

            // If Laravel did not parse JSON (parsed is empty), try manual decode and merge
            if (empty($initialParsed) && is_string($rawContent) && strlen(trim($rawContent)) > 0) {
                $decoded = json_decode($rawContent, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    // Some clients send payload under a top-level key; flatten if needed
                    if (count($decoded) === 1 && isset($decoded['data']) && is_array($decoded['data'])) {
                        $decoded = $decoded['data'];
                    }
                    $request->merge($decoded);
                    \Log::info('Applied manual JSON merge fallback', [
                        'merged_keys' => array_keys($decoded),
                    ]);
                } else {
                    \Log::warning('Manual JSON decode failed', [
                        'json_error' => json_last_error_msg(),
                        'raw_preview' => substr($rawContent, 0, 200),
                    ]);
                }
            }
            $validatedData = $request->validate([
                'company_name' => 'required|string|max:255',
                'company_size' => 'nullable|string|max:100',
                'secteur_activite' => 'nullable|string|max:255',
                'website' => ['nullable', 'string', 'max:255', 'regex:/^(https?:\/\/)?(www\.)?([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}$/i'],
                'contact_person' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'project_description' => 'required|string',
                'status' => 'nullable|in:pending,contacted,in_progress,completed,cancelled',
            ]);

            // Ensure default status if not provided
            if (!isset($validatedData['status'])) {
                $validatedData['status'] = 'pending';
            }

            $reservation = Reservation::create($validatedData);

            // Send confirmation email
            $this->sendConfirmationEmail($reservation);

            // Log initial incoming message in conversation
            try {
                \App\Models\ReservationMessage::create([
                    'reservation_id' => $reservation->id,
                    'direction' => 'incoming',
                    'subject' => $validatedData['project_description'] ? 'Nouvelle demande de réservation' : null,
                    'message' => $validatedData['project_description'],
                    'metadata' => [
                        'email' => $validatedData['email'],
                        'name' => $validatedData['contact_person'],
                        'phone' => $validatedData['phone'] ?? null,
                        'company' => $validatedData['company_name'] ?? null,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ],
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Failed to create initial reservation message', ['error' => $e->getMessage()]);
            }

            $response = [
    'message' => 'Reservation created successfully',
    'data' => $reservation,
];
return response()->json($this->sanitizeForJson($response), 201, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Failed to create reservation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to create reservation'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $reservation = Reservation::findOrFail($id);
            $response = [
    'message' => 'Reservation updated successfully',
    'data' => $reservation,
];
return response()->json($this->sanitizeForJson($response), 200, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Reservation not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $reservation = Reservation::findOrFail($id);

            $validatedData = $request->validate([
                'company_name' => 'sometimes|required|string|max:255',
                'company_size' => 'nullable|string|max:100',
                'secteur_activite' => 'nullable|string|max:255',
                'website' => ['nullable', 'string', 'max:255', 'regex:/^(https?:\/\/)?(www\.)?([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}$/i'],
                'contact_person' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|max:255',
                'phone' => 'sometimes|required|string|max:20',
                'project_description' => 'sometimes|required|string',
                'status' => 'sometimes|in:pending,contacted,in_progress,completed,cancelled',
            ]);

            $reservation->update($validatedData);

            $response = [
    'message' => 'Reservation updated successfully',
    'data' => $reservation,
];
return response()->json($this->sanitizeForJson($response), 200, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update reservation'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $reservation = Reservation::findOrFail($id);
            $reservation->delete();

           return response()->json($this->sanitizeForJson(['message' => 'Reservation deleted successfully']), 200, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete reservation'], 500);
        }
    }

    /**
     * Update reservation status
     */
    public function updateStatus(Request $request, string $id): JsonResponse
    {
        try {
            $reservation = Reservation::findOrFail($id);

            $validatedData = $request->validate([
                'status' => 'required|in:pending,contacted,in_progress,completed,cancelled',
            ]);

            $reservation->update($validatedData);

            return response()->json($this->sanitizeForJson([
    'success' => true,
    'message' => 'Réponse envoyée avec succès!',
]), 200, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update reservation status'], 500);
        }
    }

    /**
     * Reply to a reservation
     */
    public function reply(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'to_email' => 'required|email',
            'to_name' => 'required|string',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'original_message' => 'nullable|string'
        ]);

        try {
            // Load reservation to enrich template variables
            $reservation = Reservation::find((int) $validated['reservation_id']);

            // Try to render from a "reservation_reply" email template if available
            $template = \App\Models\EmailTemplate::getByType('reservation_reply');

            // Build variables for template rendering
            $variables = [
                'company_name' => config('mail.from.name') ?? config('app.name', 'TrackPro'),
                'reservation_id' => $reservation?->id,
                'contact_person' => $validated['to_name'] ?? $reservation?->contact_person,
                'email' => $validated['to_email'] ?? $reservation?->email,
                'phone' => $reservation?->phone,
                'reply_message' => $validated['message'],
                'original_message' => $validated['original_message'] ?? ($reservation?->project_description),
                'support_email' => config('mail.from.address'),
                'current_year' => date('Y'),
            ];

            // Subject and body using template if present, otherwise fall back to provided values
            if ($template) {
                $subjectToSend = $template->renderSubject(array_merge($variables, ['subject' => $validated['subject']]));
                $htmlToSend = $template->renderContent($variables);
            } else {
                \Log::info('No reservation_reply email template active; sending raw reply message');
                $subjectToSend = $validated['subject'];
                $htmlToSend = $validated['message'];
            }

            // Send email using Laravel Mail
            \Mail::send([], [], function ($message) use ($validated, $subjectToSend, $htmlToSend) {
                $message->to($validated['to_email'], $validated['to_name'])
                        ->subject($subjectToSend)
                        ->html($htmlToSend);
            });

            // Update reservation status to contacted
            $reservation = $reservation ?? Reservation::find($validated['reservation_id']);
            if ($reservation) {
                $reservation->update(['status' => 'contacted']);
            }

            // Log outgoing reply in conversation
            try {
                \App\Models\ReservationMessage::create([
                    'reservation_id' => (int) $validated['reservation_id'],
                    'direction' => 'outgoing',
                    'subject' => $validated['subject'],
                    'message' => $validated['message'],
                    'metadata' => [
                        'to_email' => $validated['to_email'],
                        'to_name' => $validated['to_name'],
                        'original_message' => $validated['original_message'] ?? null,
                    ],
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Failed to log reservation reply message', ['error' => $e->getMessage()]);
            }

            return response()->json($this->sanitizeForJson([
    'success' => true,
    'data' => $messages,
]), 200, [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get conversation messages for a reservation
     */
    public function conversation(Reservation $reservation): JsonResponse
    {
        $messages = \App\Models\ReservationMessage::where('reservation_id', $reservation->id)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $messages,
        ]);
    }

    /**
     * Send confirmation email for new reservation
     */
    private function sendConfirmationEmail($reservation)
    {
        try {
            // Get the reservation confirmation email template
            $template = \App\Models\EmailTemplate::getByType('reservation_confirmation');
            
            // Prepare variables for the template
            $contactSettings = \App\Models\ContactSetting::where('is_active', true)->first();
            $supportEmail = $contactSettings->email ?? config('mail.from.address');
            $supportPhone = $contactSettings->phone ?? $contactSettings->emergency_contact ?? null;
            $variables = [
                'company_name' => 'TrackPro Solutions',
                'reservation_id' => $reservation->id,
                'contact_person' => $reservation->contact_person,
                'company_name_client' => $reservation->company_name,
                'email' => $reservation->email,
                'phone' => $reservation->phone,
                'reservation_date' => $reservation->created_at->format('d/m/Y à H:i'),
                'tracking_url' => config('app.url') . '/reservation/' . $reservation->id,
                'support_email' => $supportEmail,
                'support_phone' => $supportPhone,
                'current_year' => date('Y'),
                'company_address' => '123 Rue de la Tech, 75001 Paris, France'
            ];

            // Render the template or build a fallback
            if ($template) {
                $subject = $template->renderSubject($variables);
                $content = $template->renderContent($variables);
            } else {
                \Log::warning('No reservation confirmation email template found, using fallback');
                $subject = 'Confirmation de votre demande de réservation - TrackPro';
                $content = "<div style='font-family:Arial,sans-serif;font-size:14px;color:#333'>"
                    . "<p>Bonjour " . e($reservation->contact_person) . ",</p>"
                    . "<p>Nous avons bien reçu votre demande de réservation.</p>"
                    . "<p>Détails de votre demande:</p>"
                    . "<ul>"
                    . "<li>Entreprise: <strong>" . e($reservation->company_name) . "</strong></li>"
                    . "<li>Email: " . e($reservation->email) . "</li>"
                    . "<li>Téléphone: " . e($reservation->phone) . "</li>"
                    . "<li>Date: " . e($reservation->created_at->format('d/m/Y à H:i')) . "</li>"
                    . "</ul>"
                    . "<p>Notre équipe vous contactera prochainement pour la suite.</p>"
                    . "<p>Cordialement,<br>TrackPro Solutions</p>"
                    . "<hr>"
                    . "<small>Support: " . e($supportEmail) . ($supportPhone ? " | Tel: " . e($supportPhone) : "") . "</small>"
                    . "</div>";
            }

            // Send the email
            \Mail::send([], [], function ($message) use ($reservation, $subject, $content) {
                $message->to($reservation->email, $reservation->contact_person)
                        ->subject($subject)
                        ->html($content);
            });

        } catch (\Exception $e) {
            \Log::error('Failed to send reservation confirmation email: ' . $e->getMessage());
        }
    }
}