<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
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
        $services = Service::active()->ordered()->get();
        $data = $this->sanitizeForJson($services);
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'icon' => 'nullable|string',
            'image' => 'nullable|string',
            'features' => 'nullable|array',
            'button_text' => 'nullable|string|max:255',
            'button_link' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'order' => 'integer|min:0',
            'is_active' => 'boolean'
        ]);

        $service = Service::create($validated);
        $data = $this->sanitizeForJson($service->toArray());
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Service créé avec succès'
        ], 201)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $service = Service::findOrFail($id);
        $data = $this->sanitizeForJson($service->toArray());
        return response()->json($data)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $service = Service::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'icon' => 'nullable|string',
            'image' => 'nullable|string',
            'features' => 'nullable|array',
            'button_text' => 'nullable|string|max:255',
            'button_link' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'order' => 'integer|min:0',
            'is_active' => 'boolean'
        ]);

        $service->update($validated);
        $data = $this->sanitizeForJson($service->toArray());
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Service mis à jour avec succès'
        ])->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $service = Service::findOrFail($id);
        $service->delete();
        return response()->json([
            'success' => true,
            'message' => 'Service supprimé avec succès'
        ]);
    }
}
