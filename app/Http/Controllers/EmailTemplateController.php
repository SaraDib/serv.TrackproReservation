<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class EmailTemplateController extends Controller
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
        try {
            $templates = EmailTemplate::orderBy('created_at', 'desc')->get();
            
            $data = $this->sanitizeForJson($templates->toArray());
            return response()->json([
                'success' => true,
                'data' => $data
            ])->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch email templates',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
 * Store a newly created resource in storage.
 */
public function store(Request $request): JsonResponse
{
    try {
         // CONVERSION: Si variables est une string JSON, la convertir en tableau
        if (is_string($request->variables)) {
            $request->merge([
                'variables' => json_decode($request->variables, true)
            ]);
            \Log::info('Variables après conversion string->array:', ['variables' => $request->variables]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255|unique:email_templates,type',
            'subject' => 'required|string|max:255',
            'html_content' => 'required|string',
            'text_content' => 'nullable|string',
            'variables' => 'present|array',
            'is_active' => 'boolean'
        ]);

        \Log::info('Données après validation:', $validated);

        // Convertir le tableau en JSON pour la base de données
        $validated['variables'] = json_encode($validated['variables']);
        
        \Log::info('Variables converties en JSON:', ['variables_json' => $validated['variables']]);

        $template = EmailTemplate::create($validated);

        $data = $this->sanitizeForJson($template->toArray());
        return response()->json([
            'success' => true,
            'message' => 'Email template created successfully',
            'data' => $data
        ], 201)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('ERREUR VALIDATION STORE:', $e->errors());
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        \Log::error('ERREUR STORE:', ['message' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Failed to create email template',
            'error' => $e->getMessage()
        ], 500);
    }
}
    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $template = EmailTemplate::findOrFail($id);
            
            $data = $this->sanitizeForJson($template->toArray());
            return response()->json([
                'success' => true,
                'data' => $data
            ])->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Email template not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch email template',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    /**
 * Update the specified resource in storage.
 */
public function update(Request $request, string $id): JsonResponse
{
    try {
        // CONVERSION: Si variables est une string JSON, la convertir en tableau
        if (is_string($request->variables)) {
            $request->merge([
                'variables' => json_decode($request->variables, true)
            ]);
            \Log::info('Variables après conversion string->array:', ['variables' => $request->variables]);
        }

        $template = EmailTemplate::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => ['required', 'string', 'max:255', Rule::unique('email_templates')->ignore($template->id)],
            'subject' => 'required|string|max:255',
            'html_content' => 'required|string',
            'text_content' => 'nullable|string',
            'variables' => 'present|array',
            'is_active' => 'boolean'
        ]);

        \Log::info('Données après validation:', $validated);

        // Convertir le tableau en JSON pour la base de données
        $validated['variables'] = json_encode($validated['variables']);
        
        \Log::info('Variables converties en JSON:', ['variables_json' => $validated['variables']]);

        $template->update($validated);

        $data = $this->sanitizeForJson($template->toArray());
        return response()->json([
            'success' => true,
            'message' => 'Email template updated successfully',
            'data' => $data
        ])->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        \Log::error('TEMPLATE NON TROUVÉ:', ['id' => $id]);
        return response()->json([
            'success' => false,
            'message' => 'Email template not found'
        ], 404);
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('ERREUR VALIDATION UPDATE:', $e->errors());
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        \Log::error('ERREUR UPDATE:', ['message' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Failed to update email template',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $template = EmailTemplate::findOrFail($id);
            $template->delete();

            return response()->json([
                'success' => true,
                'message' => 'Email template deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Email template not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete email template',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get template by type
     */
    public function getByType(string $type): JsonResponse
    {
        try {
            $template = EmailTemplate::getByType($type);
            
            if (!$template) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email template not found for type: ' . $type
                ], 404);
            }
            
            $data = $this->sanitizeForJson($template->toArray());
            return response()->json([
                'success' => true,
                'data' => $data
            ])->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch email template',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all available template types
     */
    public function getTypes(): JsonResponse
    {
        try {
            $types = EmailTemplate::select('type', 'name')
                ->where('is_active', true)
                ->distinct()
                ->orderBy('type')
                ->get()
                ->map(function ($template) {
                    return [
                        'type' => $template->type,
                        'name' => $template->name,
                        'label' => ucfirst(str_replace('_', ' ', $template->type))
                    ];
                });
            
            $data = $this->sanitizeForJson($types->toArray());
            return response()->json([
                'success' => true,
                'data' => $data
            ])->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch template types',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Preview template with sample data
     */
    public function preview(Request $request, string $id): JsonResponse
    {
        try {
            $template = EmailTemplate::findOrFail($id);
            $variables = $request->input('variables', []);
            
            $renderedContent = $template->renderContent($variables);
            $renderedSubject = $template->renderSubject($variables);
            
            $payload = [
                'success' => true,
                'data' => [
                    'subject' => $renderedSubject,
                    'html_content' => $renderedContent,
                    'variables' => $variables
                ]
            ];
            $data = $this->sanitizeForJson($payload);
            return response()->json($data)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Email template not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to preview email template',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}