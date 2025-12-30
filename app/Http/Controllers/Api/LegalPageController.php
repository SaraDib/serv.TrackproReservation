<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LegalPage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LegalPageController extends Controller
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
        $pages = LegalPage::where('is_active', true)->orderBy('id', 'asc')->get();
        $data = $this->sanitizeForJson($pages);
        return response()->json($data)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:legal_pages,slug',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $page = LegalPage::create($validated);
        $data = $this->sanitizeForJson($page->toArray());
        return response()->json($data, 201)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Display the specified resource.
     */
    public function show(LegalPage $legalPage): JsonResponse
    {
        $data = $this->sanitizeForJson($legalPage->toArray());
        return response()->json($data)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LegalPage $legalPage): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:legal_pages,slug,' . $legalPage->id,
            'content' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $legalPage->update($validated);
        $data = $this->sanitizeForJson($legalPage->toArray());
        return response()->json($data)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LegalPage $legalPage): JsonResponse
    {
        $legalPage->delete();
        return response()->json(['success' => true]);
    }
}