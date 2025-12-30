<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeaderSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HeaderSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $headerSetting = HeaderSetting::where('is_active', true)->first();
        return response()->json($headerSetting);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'logo_text' => 'required|string|max:255',
            'logo_image' => 'nullable|string',
            'navigation_items' => 'required|array',
            'cta_button_text' => 'required|string|max:255',
            'cta_button_link' => 'required|string|max:255',
            'is_active' => 'boolean'
        ]);

        $headerSetting = HeaderSetting::create($validated);
        return response()->json($headerSetting, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $headerSetting = HeaderSetting::findOrFail($id);
        return response()->json($headerSetting);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $headerSetting = HeaderSetting::findOrFail($id);
        
        $validated = $request->validate([
            'logo_text' => 'sometimes|string|max:255',
            'logo_image' => 'nullable|string',
            'navigation_items' => 'sometimes|array',
            'cta_button_text' => 'sometimes|string|max:255',
            'cta_button_link' => 'sometimes|string|max:255',
            'is_active' => 'boolean'
        ]);

        $headerSetting->update($validated);
        return response()->json($headerSetting);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $headerSetting = HeaderSetting::findOrFail($id);
        $headerSetting->delete();
        return response()->json(['message' => 'Header setting deleted successfully']);
    }
}
