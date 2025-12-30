<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FooterSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class FooterSettingController extends Controller
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
        $footerSetting = FooterSetting::where('is_active', true)->first();
        $data = $footerSetting ? $this->sanitizeForJson($footerSetting->toArray()) : null;
        return response()->json($data)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            // Drop MIME-based image validation to avoid php_fileinfo dependency
            'logo_image' => 'nullable|file|max:2048',
            'quick_links' => 'nullable|string',
            'services_links' => 'nullable|string',
            'social_links' => 'nullable|string',
            'copyright_text' => 'nullable|string',
            'privacy_policy_url' => 'nullable|string|max:255',
            'terms_of_service_url' => 'nullable|string|max:255',
            'newsletter_enabled' => 'boolean',
            'newsletter_title' => 'nullable|string|max:255',
            'newsletter_description' => 'nullable|string',
            'background_color' => 'nullable|string|max:7',
            'text_color' => 'nullable|string|max:7',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        // Safely decode JSON string fields into arrays expected by casts
        foreach (['quick_links', 'services_links', 'social_links'] as $jsonField) {
            if (isset($validated[$jsonField]) && is_string($validated[$jsonField])) {
                try {
                    $decoded = json_decode($validated[$jsonField], true, 512, JSON_THROW_ON_ERROR);
                    // Ensure array structure
                    if (is_array($decoded)) {
                        $validated[$jsonField] = $decoded;
                    } else {
                        // If decoded to non-array, null it to avoid DB JSON errors
                        $validated[$jsonField] = null;
                    }
                } catch (\Throwable $e) {
                    // Invalid JSON; null to prevent breaking APIs
                    $validated[$jsonField] = null;
                }
            }
        }

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

        $footerSetting = FooterSetting::create($validated);
        $data = $this->sanitizeForJson($footerSetting->toArray());
        return response()->json($data, 201)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $footerSetting = FooterSetting::findOrFail($id);
        $data = $this->sanitizeForJson($footerSetting->toArray());
        return response()->json($data)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $footerSetting = FooterSetting::findOrFail($id);
        
        $validated = $request->validate([
            'company_name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            // Drop MIME-based image validation to avoid php_fileinfo dependency
            'logo_image' => 'nullable|file|max:2048',
            'quick_links' => 'nullable|string',
            'services_links' => 'nullable|string',
            'social_links' => 'nullable|string',
            'copyright_text' => 'nullable|string',
            'privacy_policy_url' => 'nullable|string|max:255',
            'terms_of_service_url' => 'nullable|string|max:255',
            'newsletter_enabled' => 'boolean',
            'newsletter_title' => 'nullable|string|max:255',
            'newsletter_description' => 'nullable|string',
            'background_color' => 'nullable|string|max:7',
            'text_color' => 'nullable|string|max:7',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        // Safely decode JSON string fields into arrays expected by casts
        foreach (['quick_links', 'services_links', 'social_links'] as $jsonField) {
            if (isset($validated[$jsonField]) && is_string($validated[$jsonField])) {
                try {
                    $decoded = json_decode($validated[$jsonField], true, 512, JSON_THROW_ON_ERROR);
                    if (is_array($decoded)) {
                        $validated[$jsonField] = $decoded;
                    } else {
                        $validated[$jsonField] = null;
                    }
                } catch (\Throwable $e) {
                    $validated[$jsonField] = null;
                }
            }
        }

        // Handle explicit removal of the existing logo image when requested
        // If the client sends a flag like `remove_logo_image=true` and no new file, clear the logo
        if ($request->boolean('remove_logo_image')) {
            // Delete old image file if exists
            if ($footerSetting->logo_image && Storage::disk('public')->exists(str_replace('/storage/', '', $footerSetting->logo_image))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $footerSetting->logo_image));
            }
            // Set the logo_image to null in the database
            $validated['logo_image'] = null;
        }

        // Handle file upload
        if ($request->hasFile('logo_image')) {
            // Delete old image if exists
            if ($footerSetting->logo_image && Storage::disk('public')->exists(str_replace('/storage/', '', $footerSetting->logo_image))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $footerSetting->logo_image));
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

        $footerSetting->update($validated);
        $data = $this->sanitizeForJson($footerSetting->toArray());
        return response()->json($data)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $footerSetting = FooterSetting::findOrFail($id);
        $footerSetting->delete();
        return response()->json(['message' => 'Footer setting deleted successfully']);
    }
}
