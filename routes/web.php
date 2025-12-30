<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\LogController;

Route::get('/', function () {
    return view('welcome');
});

// Quick sanity route to verify web routes are loaded
Route::get('/ping', function () {
    return response('pong', 200);
});

// Introspection: dump current routes as JSON (dev-only)
Route::get('/routes-json', function () {
    $routes = collect(\Illuminate\Support\Facades\Route::getRoutes())->map(function ($route) {
        return [
            'uri' => $route->uri(),
            'methods' => $route->methods(),
            'name' => $route->getName(),
            'action' => $route->getActionName(),
        ];
    })->values();
    return response()->json($routes);
});

// Temporary fallback API group (mirrors routes/api.php) to bypass 404 on /api/v1/*
/* Route::prefix('api/v1')->group(function () {
    // Services (partial: index only for now to unblock UI)
    Route::get('services', [ServiceController::class, 'index']);

    // Logs (GET list, POST store) used by frontend error reporting
    Route::get('logs', [LogController::class, 'index']);
    Route::post('logs', [LogController::class, 'store']);
}); */
