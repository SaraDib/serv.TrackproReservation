<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HeroSlideController extends Controller
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
        $heroSlides = HeroSlide::active()->ordered()->get();
        $data = $this->sanitizeForJson($heroSlides);
        return response()->json($data)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
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
            'button_text' => 'nullable|string|max:255',
            'button_link' => 'nullable|string|max:255',
            'background_image' => 'nullable|string',
            'background_color' => 'nullable|string|max:7',
            'order' => 'integer|min:0',
            'is_active' => 'boolean'
        ]);

        $heroSlide = HeroSlide::create($validated);
        $data = $this->sanitizeForJson($heroSlide->toArray());
        return response()->json($data, 201)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $heroSlide = HeroSlide::findOrFail($id);
        $data = $this->sanitizeForJson($heroSlide->toArray());
        return response()->json($data)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $heroSlide = HeroSlide::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'button_link' => 'nullable|string|max:255',
            'background_image' => 'nullable|string',
            'background_color' => 'nullable|string|max:7',
            'order' => 'integer|min:0',
            'is_active' => 'boolean'
        ]);

        $heroSlide->update($validated);
        $data = $this->sanitizeForJson($heroSlide->toArray());
        return response()->json($data)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $heroSlide = HeroSlide::findOrFail($id);
        $heroSlide->delete();
        return response()->json(['message' => 'Hero slide deleted successfully']);
    }
}
