<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class LogController extends Controller
{
    /**
     * Return the last N lines from the application log file.
     */
    public function index(Request $request): JsonResponse
    {
        $limit = (int)($request->query('limit', 8));
        if ($limit <= 0) { $limit = 8; }
        if ($limit > 200) { $limit = 200; }

        // Determine log file path from configuration (single channel path)
        $path = Config::get('logging.channels.single.path');
        if (!$path || !File::exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Log file not found.',
                'path' => $path,
            ], 404);
        }

        try {
            $contents = File::get($path);
            $lines = preg_split('/\r?\n/', $contents);
            // Get last N non-empty lines
            $lines = array_values(array_filter($lines, fn($l) => strlen(trim($l)) > 0));
            $count = count($lines);
            $start = max(0, $count - $limit);
            $lastLines = array_slice($lines, $start);

            return response()->json([
                'success' => true,
                'data' => [
                    'path' => $path,
                    'limit' => $limit,
                    'count' => $count,
                    'lines' => $lastLines,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to read log file.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Store a log entry coming from the frontend or other clients.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'level' => 'required|string',
            'message' => 'required|string',
            'context' => 'nullable|array',
            'url' => 'nullable|string',
            'user_agent' => 'nullable|string',
            'ts' => 'nullable|numeric',
        ]);

        // Normalize log level to Monolog-compatible names
        $level = strtolower($validated['level']);
        $levelMap = [
            'warn' => 'warning',
            'warning' => 'warning',
            'error' => 'error',
            'info' => 'info',
            'debug' => 'debug',
            'notice' => 'notice',
            'trace' => 'debug',
        ];
        $normalizedLevel = $levelMap[$level] ?? 'info';

        $context = $validated['context'] ?? [];
        // Attach meta context for better traceability
        $context['meta'] = [
            'url' => $validated['url'] ?? $request->header('Referer'),
            'user_agent' => $validated['user_agent'] ?? $request->userAgent(),
            'ts' => $validated['ts'] ?? round(microtime(true) * 1000),
            'ip' => $request->ip(),
        ];

        // Log using Laravel's logger (stack channel by default)
        Log::log($normalizedLevel, $validated['message'], $context);

        return response()->json([
            'success' => true,
            'status' => 'logged',
        ], 201);
    }
}