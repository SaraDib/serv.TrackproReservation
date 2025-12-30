<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AboutSection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class AboutSectionController extends Controller
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
        $aboutSections = AboutSection::orderBy('is_active', 'desc')->orderBy('id', 'asc')->get();
        $data = $this->sanitizeForJson($aboutSections);
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
        // Build dynamic validation rules depending on whether a file is uploaded
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'features' => 'nullable', // can be array or JSON string
            'flip_prefix' => 'nullable|string|max:255',
            'flip_suffix' => 'nullable|string|max:255',
            'flip_lines' => 'nullable', // can be array or JSON string
            'button_text' => 'nullable|string|max:255',
            'button_link' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'remove_image' => 'nullable' // flag for deletion on update, ignored on create
        ];
        if ($request->hasFile('image')) {
            $rules['image'] = 'nullable|file|image|max:4096';
        } else {
            $rules['image'] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        // Normalize features: accept JSON string or array
        if (isset($validated['features'])) {
            if (is_string($validated['features'])) {
                $decoded = json_decode($validated['features'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $validated['features'] = $decoded;
                } else {
                    // Invalid JSON string
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid features JSON provided'
                    ], 422);
                }
            }
        }

        // Normalize flip_lines: accept JSON string or array
        if (isset($validated['flip_lines'])) {
            if (is_string($validated['flip_lines'])) {
                $decoded = json_decode($validated['flip_lines'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $validated['flip_lines'] = $decoded;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid flip_lines JSON provided'
                    ], 422);
                }
            }
        }

        // Handle image upload if file provided
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('about', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        $aboutSection = AboutSection::create($validated);
        return response()->json([
            'success' => true,
            'data' => $aboutSection
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $aboutSection = AboutSection::findOrFail($id);
        $data = $this->sanitizeForJson($aboutSection->toArray());
        return response()->json([
            'success' => true,
            'data' => $data
        ])->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $aboutSection = AboutSection::findOrFail($id);

        // Build dynamic validation rules
        $rules = [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'features' => 'nullable',
            'flip_prefix' => 'nullable|string|max:255',
            'flip_suffix' => 'nullable|string|max:255',
            'flip_lines' => 'nullable',
            'button_text' => 'nullable|string|max:255',
            'button_link' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'remove_image' => 'nullable'
        ];
        if ($request->hasFile('image')) {
            $rules['image'] = 'nullable|file|image|max:4096';
        } else {
            $rules['image'] = 'nullable|string';
        }

        $validated = $request->validate($rules);

        // Normalize features
        if (isset($validated['features'])) {
            if (is_string($validated['features'])) {
                $decoded = json_decode($validated['features'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $validated['features'] = $decoded;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid features JSON provided'
                    ], 422);
                }
            }
        }

        // Normalize flip_lines
        if (isset($validated['flip_lines'])) {
            if (is_string($validated['flip_lines'])) {
                $decoded = json_decode($validated['flip_lines'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $validated['flip_lines'] = $decoded;
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid flip_lines JSON provided'
                    ], 422);
                }
            }
        }

        // Handle image deletion request
        if ($request->boolean('remove_image')) {
            if (!empty($aboutSection->image)) {
                // delete old file if exists
                $relative = str_replace('/storage/', '', $aboutSection->image);
                Storage::disk('public')->delete($relative);
            }
            $validated['image'] = null;
        }

        // Handle new image upload
        if ($request->hasFile('image')) {
            // delete old file if exists
            if (!empty($aboutSection->image)) {
                $relative = str_replace('/storage/', '', $aboutSection->image);
                Storage::disk('public')->delete($relative);
            }
            $path = $request->file('image')->store('about', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        $aboutSection->update($validated);
        return response()->json([
            'success' => true,
            'data' => $aboutSection->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $aboutSection = AboutSection::findOrFail($id);
        $aboutSection->delete();
        return response()->json([
            'success' => true,
            'message' => 'About section deleted successfully'
        ]);
    }
}
