<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeaderSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class HeaderController extends Controller
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
        $headerSetting = HeaderSetting::first();
        $data = $headerSetting ? $this->sanitizeForJson($headerSetting->toArray()) : null;
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
        $validated = $request->validate([
            'logo_text' => 'required|string|max:255',
            // Drop MIME-based image validation to avoid php_fileinfo dependency
            'logo_image' => 'nullable|file|max:2048',
            'is_active' => 'boolean',
            // Allow optional advanced fields if provided by clients
            'navigation_items' => 'sometimes|array',
            'cta_button_text' => 'sometimes|string|max:255',
            'cta_button_link' => 'sometimes|string|max:255'
        ]);

        // Handle file upload
        if ($request->hasFile('logo_image')) {
            $file = $request->file('logo_image');
            // Simple extension-based safeguard (does not require php_fileinfo)
            $ext = strtolower($file->getClientOriginalExtension());
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            if (!in_array($ext, $allowed)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image format. Allowed: jpg, jpeg, png, gif, webp, svg.'
                ], 422);
            }
            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('logos', $filename, 'public');
            $validated['logo_image'] = '/storage/' . $path;
        }

        // Ensure required columns exist even if the frontend doesn't send them
        if (!array_key_exists('navigation_items', $validated)) {
            $validated['navigation_items'] = [];
        }
        if (!array_key_exists('cta_button_text', $validated)) {
            $validated['cta_button_text'] = 'Get Started';
        }
        if (!array_key_exists('cta_button_link', $validated)) {
            $validated['cta_button_link'] = '#contact';
        }

        $headerSetting = HeaderSetting::create($validated);
        return response()->json([
            'success' => true,
            'data' => $headerSetting
        ], 201);
    }

    /**
     * Display the specified resource.
     */
        public function show(string $id): JsonResponse
    {
        $headerSetting = HeaderSetting::findOrFail($id);
        $data = $this->sanitizeForJson($headerSetting->toArray());
        return response()->json($data)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $headerSetting = HeaderSetting::findOrFail($id);
        
        $validated = $request->validate([
            'logo_text' => 'sometimes|string|max:255',
            // Drop MIME-based image validation to avoid php_fileinfo dependency
            'logo_image' => 'nullable|file|max:2048',
            'is_active' => 'boolean'
        ]);

        // Handle explicit removal of the existing logo image when requested
        // If the client sends a flag like `remove_logo_image=true` and no new file, clear the logo
        if ($request->boolean('remove_logo_image')) {
            // Delete old image file if exists
            if ($headerSetting->logo_image && Storage::disk('public')->exists(str_replace('/storage/', '', $headerSetting->logo_image))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $headerSetting->logo_image));
            }
            // Set the logo_image to null in the database
            $validated['logo_image'] = null;
        }

        // Handle file upload
        if ($request->hasFile('logo_image')) {
            // Delete old image if exists
            if ($headerSetting->logo_image && Storage::disk('public')->exists(str_replace('/storage/', '', $headerSetting->logo_image))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $headerSetting->logo_image));
            }
            
            $file = $request->file('logo_image');
            // Simple extension-based safeguard (does not require php_fileinfo)
            $ext = strtolower($file->getClientOriginalExtension());
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            if (!in_array($ext, $allowed)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image format. Allowed: jpg, jpeg, png, gif, webp, svg.'
                ], 422);
            }
            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $file->getClientOriginalName());
            $path = $file->storeAs('logos', $filename, 'public');
            $validated['logo_image'] = '/storage/' . $path;
        }

        $headerSetting->update($validated);
        return response()->json([
            'success' => true,
            'data' => $headerSetting->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $headerSetting = HeaderSetting::findOrFail($id);
        
        // Delete associated image file
        if ($headerSetting->logo_image && Storage::disk('public')->exists(str_replace('/storage/', '', $headerSetting->logo_image))) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $headerSetting->logo_image));
        }
        
        $headerSetting->delete();
        return response()->json([
            'success' => true,
            'message' => 'Header deleted successfully'
        ]);
    }
}
