<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\AboutSection;
use App\Models\ContactSetting;
use App\Models\HeaderSetting;
use App\Models\FooterSetting;
use App\Models\HeroSlide;
use App\Models\Service;

class SiteContentController extends Controller
{
    /**
     * Recursively sanitize data to ensure valid UTF-8 for JSON encoding.
     */
    private function sanitizeForJson($data)
    {
        if (is_array($data)) {
            $clean = [];
            foreach ($data as $key => $value) {
                $clean[$key] = $this->sanitizeForJson($value);
            }
            return $clean;
        }

        if (is_string($data)) {
            if (!mb_check_encoding($data, 'UTF-8')) {
                $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
            }
            return $data;
        }

        return $data;
    }
    /**
     * Return aggregated site content in one payload.
     */
    public function index(): JsonResponse
    {
        try {
            $about = AboutSection::where('is_active', true)->first();
            $contact = ContactSetting::where('is_active', true)->first();
            $header = HeaderSetting::where('is_active', true)->first();
            $footer = FooterSetting::where('is_active', true)->first();
            $heroSlides = HeroSlide::where('is_active', true)->orderBy('order')->get();
            $services = Service::active()->ordered()->get();

            $payload = [
                'about' => optional($about)->toArray(),
                'contact' => optional($contact)->toArray(),
                'header' => optional($header)->toArray(),
                'footer' => optional($footer)->toArray(),
                'hero_slides' => $heroSlides ? $heroSlides->toArray() : [],
                'services' => $services ? $services->toArray() : [],
            ];

            $clean = $this->sanitizeForJson($payload);

            return response()->json($clean, 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (\Throwable $e) {
            \Log::error('Failed to load aggregated site content', [
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'error' => 'Failed to fetch site content'
            ], 500);
        }
    }
}