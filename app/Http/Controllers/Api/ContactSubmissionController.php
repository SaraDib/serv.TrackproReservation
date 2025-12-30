<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactSubmission;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class ContactSubmissionController extends Controller
{
    /**
     * Recursively sanitize values for JSON encoding by removing invalid UTF-8.
     */
    private function sanitizeForJson($value)
    {
        if (is_string($value)) {
            if (function_exists('mb_check_encoding') && !mb_check_encoding($value, 'UTF-8')) {
                $clean = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
                return $clean !== false ? $clean : '';
            }
            return $value;
        }
        if ($value instanceof \Illuminate\Database\Eloquent\Model) {
            return $this->sanitizeForJson($value->toArray());
        }
        if ($value instanceof \Illuminate\Support\Collection) {
            return $this->sanitizeForJson($value->toArray());
        }
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->sanitizeForJson($v);
            }
            return $value;
        }
        return $value;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $submissions = ContactSubmission::orderBy('created_at', 'desc')->get();
        
        $data = $this->sanitizeForJson($submissions->toArray());
        return response()->json([
            'success' => true,
            'data' => $data
        ])->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'subject' => 'required|string|max:255',
                'message' => 'required|string',
                'company' => 'nullable|string|max:255',
                'service_interest' => 'nullable|string|max:255',
            ]);

            $contactSubmission = ContactSubmission::create($validatedData);

            // Log initial incoming message in conversation
            \App\Models\ContactSubmissionMessage::create([
                'contact_submission_id' => $contactSubmission->id,
                'direction' => 'incoming',
                'subject' => $validatedData['subject'] ?? null,
                'message' => $validatedData['message'],
                'metadata' => [
                    'email' => $validatedData['email'],
                    'name' => $validatedData['name'],
                    'phone' => $validatedData['phone'] ?? null,
                    'company' => $validatedData['company'] ?? null,
                    'service_interest' => $validatedData['service_interest'] ?? null,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
            ]);

            // Send confirmation email
            $this->sendConfirmationEmail($contactSubmission);

            $data = $this->sanitizeForJson($contactSubmission->toArray());
            return response()->json([
                'message' => 'Contact submission created successfully',
                'data' => $data
            ], 201)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create contact submission'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ContactSubmission $contactSubmission): JsonResponse
    {
        $data = $this->sanitizeForJson($contactSubmission->toArray());
        return response()->json([
            'success' => true,
            'data' => $data
        ])->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContactSubmission $contactSubmission): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in([
                ContactSubmission::STATUS_NEW,
                ContactSubmission::STATUS_READ,
                ContactSubmission::STATUS_REPLIED,
                ContactSubmission::STATUS_ARCHIVED
            ])],
        ]);

        $contactSubmission->update($validated);

        $data = $this->sanitizeForJson($contactSubmission->toArray());
        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès!',
            'data' => $data
        ])->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactSubmission $contactSubmission): JsonResponse
    {
        $contactSubmission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message supprimé avec succès!'
        ]);
    }

    /**
     * Mark submission as read
     */
    public function markAsRead(ContactSubmission $contactSubmission): JsonResponse
    {
        $contactSubmission->update(['status' => ContactSubmission::STATUS_READ]);

        $data = $this->sanitizeForJson($contactSubmission->toArray());
        return response()->json([
            'success' => true,
            'message' => 'Message marqué comme lu!',
            'data' => $data
        ])->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Get statistics
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total' => ContactSubmission::count(),
            'new' => ContactSubmission::where('status', ContactSubmission::STATUS_NEW)->count(),
            'read' => ContactSubmission::where('status', ContactSubmission::STATUS_READ)->count(),
            'replied' => ContactSubmission::where('status', ContactSubmission::STATUS_REPLIED)->count(),
            'archived' => ContactSubmission::where('status', ContactSubmission::STATUS_ARCHIVED)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ])->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Reply to a contact submission
     */
    public function reply(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'submission_id' => 'required|exists:contact_submissions,id',
            'to_email' => 'required|email',
            'to_name' => 'required|string',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'original_message' => 'nullable|string'
        ]);

        try {
            // Try to render from an "contact_reply" email template if available
            $template = \App\Models\EmailTemplate::getByType('contact_reply');

            // Build variables for template rendering
            $variables = [
                'company_name' => config('mail.from.name') ?? config('app.name', 'TrackPro'),
                'contact_name' => $validated['to_name'],
                'contact_email' => $validated['to_email'],
                'reply_message' => $validated['message'],
                'original_message' => $validated['original_message'] ?? '',
                'support_email' => config('mail.from.address'),
                'current_year' => date('Y'),
            ];

            // Subject and body using template if present, otherwise fall back to provided values
            if ($template) {
                $subjectToSend = $template->renderSubject(array_merge($variables, ['subject' => $validated['subject']]));
                $htmlToSend = $template->renderContent($variables);
            } else {
                \Log::info('No contact_reply email template active; sending raw reply message');
                $subjectToSend = $validated['subject'];
                $htmlToSend = $validated['message'];
            }

            // Send email using Laravel Mail with explicit headers
            Mail::send([], [], function ($message) use ($validated, $subjectToSend, $htmlToSend) {
                $message->from(config('mail.from.address'), config('mail.from.name'))
                        ->to($validated['to_email'], $validated['to_name'])
                        ->replyTo(config('mail.from.address'), config('mail.from.name'))
                        ->subject($subjectToSend)
                        ->html($htmlToSend);
                // Add helpful headers
                try {
                    $symfony = $message->getSymfonyMessage();
                    $symfony->getHeaders()->addTextHeader('X-Mailer', 'TrackPro');
                } catch (\Throwable $e) {
                    // Header additions are best-effort
                }
            });

            // Update submission status to replied
            $submission = ContactSubmission::find($validated['submission_id']);
            $submission->update(['status' => ContactSubmission::STATUS_REPLIED]);

            // Log outgoing reply in conversation
            \App\Models\ContactSubmissionMessage::create([
                'contact_submission_id' => (int) $validated['submission_id'],
                'direction' => 'outgoing',
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'metadata' => [
                    'to_email' => $validated['to_email'],
                    'to_name' => $validated['to_name'],
                    'original_message' => $validated['original_message'] ?? null,
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Réponse envoyée avec succès!'
            ])->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage()
            ], 500)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        }
    }

    /**
     * Get conversation messages for a submission
     */
    public function conversation(ContactSubmission $contactSubmission): JsonResponse
    {
        $messages = \App\Models\ContactSubmissionMessage::where('contact_submission_id', $contactSubmission->id)
            ->orderBy('created_at', 'asc')
            ->get();

        $data = $this->sanitizeForJson($messages->toArray());
        return response()->json([
            'success' => true,
            'data' => $data,
        ])->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Send confirmation email for new contact submission
     */
    private function sendConfirmationEmail($contactSubmission)
    {
        try {
            \Log::info('Effective mail config at send', [
                'default' => config('mail.default'),
                'smtp' => config('mail.mailers.smtp'),
                'from' => config('mail.from'),
            ]);
            // Prepare variables for the template
            $variables = [
                'company_name' => 'TrackPro Solutions',
                'contact_name' => $contactSubmission->name,
                'contact_email' => $contactSubmission->email,
                'contact_phone' => $contactSubmission->phone ?? 'Non fourni',
                'contact_company' => $contactSubmission->company ?? 'Non fourni',
                'subject' => $contactSubmission->subject,
                'message' => $contactSubmission->message,
                'service_interest' => $contactSubmission->service_interest ?? 'Non spécifié',
                'submission_date' => $contactSubmission->created_at->format('d/m/Y à H:i'),
                'support_email' => config('mail.from.address'),
                'support_phone' => '+33 1 23 45 67 89',
                'current_year' => date('Y'),
                'company_address' => '123 Rue de la Tech, 75001 Paris, France'
            ];

            // Get the contact confirmation email template (optional)
            $template = \App\Models\EmailTemplate::getByType('contact_confirmation');

            // Render the template or build a fallback if missing
            if ($template) {
                $subject = $template->renderSubject($variables);
                $content = $template->renderContent($variables);
            } else {
                \Log::warning('No contact confirmation email template found, using fallback');
                $subject = 'Confirmation de réception de votre message - TrackPro';
                $content = "<div style='font-family:Arial,sans-serif;font-size:14px;color:#333'>"
                    . "<p>Bonjour " . e($contactSubmission->name) . ",</p>"
                    . "<p>Nous avons bien reçu votre message et vous remercions de nous avoir contactés.</p>"
                    . "<p>Détails de votre message:</p>"
                    . "<ul>"
                    . "<li>Sujet: <strong>" . e($contactSubmission->subject) . "</strong></li>"
                    . "<li>Email: " . e($contactSubmission->email) . "</li>"
                    . (isset($contactSubmission->phone) ? "<li>Téléphone: " . e($contactSubmission->phone) . "</li>" : "")
                    . (isset($contactSubmission->company) ? "<li>Entreprise: " . e($contactSubmission->company) . "</li>" : "")
                    . "<li>Date d'envoi: " . e($contactSubmission->created_at->format('d/m/Y à H:i')) . "</li>"
                    . "</ul>"
                    . "<p>Notre équipe vous répondra dans les plus brefs délais.</p>"
                    . "<p>Cordialement,<br>TrackPro Solutions</p>"
                    . "<hr>"
                    . "<small>Support: " . e(config('mail.from.address')) . " | Tel: +33 1 23 45 67 89</small>"
                    . "</div>";
            }

            // Send the email with explicit From/Reply-To to contact
            Mail::send([], [], function ($message) use ($contactSubmission, $subject, $content) {
                $message->from(config('mail.from.address'), config('mail.from.name'))
                        ->to($contactSubmission->email, $contactSubmission->name)
                        ->replyTo(config('mail.from.address'), config('mail.from.name'))
                        ->subject($subject)
                        ->html($content);
                try {
                    $symfony = $message->getSymfonyMessage();
                    $symfony->getHeaders()->addTextHeader('X-Mailer', 'TrackPro');
                } catch (\Throwable $e) {
                    // Header additions are best-effort
                }
            });

            // Send admin notification
            $adminAddress = config('mail.from.address');
            $adminName = config('mail.from.name');
            $adminSubject = 'Nouvelle demande de contact reçue';
            $adminBody = view('emails.contact_admin_notification', [
                'submission' => $contactSubmission,
            ])->render();

            Mail::send([], [], function ($message) use ($adminAddress, $adminName, $adminSubject, $adminBody) {
                $message->from($adminAddress, $adminName)
                        ->to($adminAddress, $adminName)
                        ->subject($adminSubject)
                        ->html($adminBody);
                try {
                    $symfony = $message->getSymfonyMessage();
                    $symfony->getHeaders()->addTextHeader('X-Mailer', 'TrackPro');
                } catch (\Throwable $e) {}
            });

        } catch (\Exception $e) {
            \Log::error('Failed to send contact confirmation email: ' . $e->getMessage());
        }
    }
}