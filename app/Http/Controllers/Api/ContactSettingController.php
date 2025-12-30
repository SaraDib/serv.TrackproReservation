<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactSetting;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ContactSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $contactSetting = ContactSetting::where('is_active', true)->first();
        return response()->json($contactSetting);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'whatsapp' => 'nullable|string|max:20',
            'emergency_contact' => 'nullable|string|max:20',
            'office_hours' => 'nullable|string',
            'map_embed' => 'nullable|string',
            'contact_form_enabled' => 'boolean',
            'auto_reply_enabled' => 'boolean',
            'auto_reply_message' => 'nullable|string',
            'social_links' => 'nullable|array',
            'form_settings' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        $contactSetting = ContactSetting::create($validated);
        return response()->json($contactSetting, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $contactSetting = ContactSetting::findOrFail($id);
        return response()->json($contactSetting);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $contactSetting = ContactSetting::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'email' => 'sometimes|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'whatsapp' => 'nullable|string|max:20',
            'emergency_contact' => 'nullable|string|max:20',
            'office_hours' => 'nullable|string',
            'map_embed' => 'nullable|string',
            'contact_form_enabled' => 'boolean',
            'auto_reply_enabled' => 'boolean',
            'auto_reply_message' => 'nullable|string',
            'social_links' => 'nullable|array',
            'form_settings' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        $contactSetting->update($validated);
        return response()->json($contactSetting);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $contactSetting = ContactSetting::findOrFail($id);
        $contactSetting->delete();
        return response()->json(['message' => 'Contact setting deleted successfully']);
    }
}
