<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HeaderSettingController;
use App\Http\Controllers\Api\HeaderController;
use App\Http\Controllers\Api\HeroSlideController;
use App\Http\Controllers\Api\AboutSectionController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ContactSettingController;
use App\Http\Controllers\Api\ContactSubmissionController;
use App\Http\Controllers\Api\FooterSettingController;
use App\Http\Controllers\Api\LegalPageController;
use App\Http\Controllers\Api\LogController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SiteContentController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\EmailTemplateController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API Routes for dynamic content
Route::prefix('v1')->middleware('throttle:api')->group(function () {
    // Auth
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);

    // Aggregated site content
    Route::get('site-content', [SiteContentController::class, 'index']);

    // Dev-only helpers
    Route::post('dev/bootstrap-admin', [AuthController::class, 'bootstrapAdmin']);
    // Header Settings
    Route::apiResource('header-settings', HeaderSettingController::class);
    
    // Headers (with file upload support)
    Route::apiResource('headers', HeaderController::class);
    
    // Hero Slides
    Route::apiResource('hero-slides', HeroSlideController::class);
    
    // About Section
    Route::apiResource('about-sections', AboutSectionController::class);
    
    // Services
    Route::apiResource('services', ServiceController::class);
    
    // Contact Settings
    Route::apiResource('contact-settings', ContactSettingController::class);
    
    // Contact Submissions
    Route::apiResource('contact-submissions', ContactSubmissionController::class);
    Route::patch('contact-submissions/{contactSubmission}/mark-as-read', [ContactSubmissionController::class, 'markAsRead']);
    Route::get('contact-submissions-statistics', [ContactSubmissionController::class, 'statistics']);
    Route::post('contact-submissions/reply', [ContactSubmissionController::class, 'reply']);
    Route::get('contact-submissions/{contactSubmission}/conversation', [ContactSubmissionController::class, 'conversation']);
    
    // Footer Settings
    Route::apiResource('footer-settings', FooterSettingController::class);

    // Legal Pages (Privacy Policy, Terms of Service)
    Route::apiResource('legal-pages', LegalPageController::class);

    // System Logs endpoint
    Route::get('logs', [LogController::class, 'index'])->middleware('throttle:logs');
    Route::post('logs', [LogController::class, 'store'])->middleware('throttle:logs');
    
    // Reservations
    Route::apiResource('reservations', ReservationController::class);
    Route::patch('reservations/{id}/status', [ReservationController::class, 'updateStatus']);
    Route::post('reservations/reply', [ReservationController::class, 'reply']);
    Route::get('reservations/{reservation}/conversation', [ReservationController::class, 'conversation']);
    
    // Email Templates
    Route::get('email-templates/types', [EmailTemplateController::class, 'getTypes']);
    Route::get('email-templates/type/{type}', [EmailTemplateController::class, 'getByType']);
    Route::post('email-templates/{id}/preview', [EmailTemplateController::class, 'preview']);
    Route::apiResource('email-templates', EmailTemplateController::class);

    // Users Management
    Route::apiResource('users', \App\Http\Controllers\Api\UserController::class);
});
