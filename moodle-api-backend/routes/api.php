<?php
// routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MoodleAuthController;
use App\Http\Controllers\MoodleCourseController;
use App\Http\Controllers\MoodleCertificateController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Test route to verify API is working
Route::get('/test', function () {
    return response()->json(['message' => 'API routes are working!']);
});

// Example route for your mobile app's internal user authentication
// You would typically use Laravel Sanctum for this.
// For demonstration, we'll assume a user is authenticated via Sanctum
// and their ID is available via Auth::id().
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Moodle Integration API Endpoints
    Route::prefix('v1/moodle')->group(function () {
        // Moodle Account Linking
        // This endpoint links a mobile app user to their Moodle account by obtaining and storing a Moodle token.
        Route::post('auth/link', [MoodleAuthController::class, 'linkMoodleAccount']);
        // This endpoint unlinks a mobile app user's Moodle account by removing the stored token.
        Route::post('auth/unlink', [MoodleAuthController::class, 'unlinkMoodleAccount']);

        // Course Access
        // Fetches courses the linked Moodle user is enrolled in, categorized by status.
        Route::get('courses/enrolled', [MoodleCourseController::class, 'getEnrolledCourses']);
        // Fetches all courses available on the Moodle site that the linked user has permission to browse.
        Route::get('courses/available', [MoodleCourseController::class, 'getAvailableCourses']);
        // Attempts to enroll the linked Moodle user in a specific course (if self-enrollment is enabled).
        Route::post('courses/{course_id}/enroll', [MoodleCourseController::class, 'enrollUserInCourse']);

        // Certificates
        // Fetches all available certificates for the linked Moodle user.
        Route::get('certificates', [MoodleCertificateController::class, 'getUserCertificates']);
    });
}); 