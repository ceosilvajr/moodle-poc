<?php
// app/Http/Controllers/MoodleAuthController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MoodleApiService;
use App\Models\UserMoodleToken;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * Handles Moodle account linking and unlinking for authenticated mobile app users.
 */
class MoodleAuthController extends Controller
{
    protected MoodleApiService $moodleApiService;

    public function __construct(MoodleApiService $moodleApiService)
    {
        $this->moodleApiService = $moodleApiService;
    }

    /**
     * Links a mobile app user's account to their Moodle account.
     *
     * @param Request $request The incoming HTTP request containing Moodle credentials.
     * @return \Illuminate\Http\JsonResponse
     */
    public function linkMoodleAccount(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'moodle_username' => 'required|string',
            'moodle_password' => 'required|string',
        ]);

        // Get the authenticated mobile app user's ID
        // Assumes Laravel Sanctum or similar authentication is set up for your backend
        $mobileUserId = Auth::id();
        if (!$mobileUserId) {
            return response()->json(['error' => 'Unauthorized: Mobile user not authenticated with backend.'], 401);
        }

        // 1. Obtain Moodle Token from Moodle
        $moodleToken = $this->moodleApiService->getMoodleToken(
            $request->moodle_username,
            $request->moodle_password
        );

        if (!$moodleToken) {
            return response()->json(['error' => 'Failed to obtain Moodle token. Please check Moodle credentials and server configuration.'], 400);
        }

        // 2. Get Moodle User ID using the obtained token
        // This is important to link your internal user to Moodle's internal user ID
        $siteInfo = $this->moodleApiService->callMoodleApi($moodleToken, 'core_webservice_get_site_info');

        if (isset($siteInfo['error'])) {
            Log::error("Failed to get Moodle site info after token: " . ($siteInfo['error'] ?? 'Unknown error'));
            return response()->json(['error' => 'Failed to retrieve Moodle user information after successful token acquisition.'], 500);
        }

        $moodleUserId = $siteInfo['userid'] ?? null;
        if (!$moodleUserId) {
            return response()->json(['error' => 'Moodle user ID not found in Moodle site info response.'], 500);
        }

        // 3. Store Moodle Token and User ID in your backend database, linked to your mobile user
        try {
            UserMoodleToken::updateOrCreate(
                ['mobile_user_id' => $mobileUserId], // Find by mobile user ID
                [
                    'moodle_token' => $moodleToken,
                    'moodle_user_id' => $moodleUserId,
                    'moodle_username' => $request->moodle_username,
                ]
            );
        } catch (\Exception $e) {
            Log::error("Database error storing Moodle token: " . $e->getMessage());
            return response()->json(['error' => 'Failed to store Moodle account link in database.'], 500);
        }

        return response()->json(['status' => 'success', 'message' => 'Moodle account linked successfully.'], 200);
    }

    /**
     * Unlinks a mobile app user's Moodle account.
     *
     * @param Request $request The incoming HTTP request.
     * @return \Illuminate\Http\JsonResponse
     */
    public function unlinkMoodleAccount(Request $request)
    {
        $mobileUserId = Auth::id();
        if (!$mobileUserId) {
            return response()->json(['error' => 'Unauthorized: Mobile user not authenticated with backend.'], 401);
        }

        // Delete the Moodle token record for the current mobile user
        try {
            UserMoodleToken::where('mobile_user_id', $mobileUserId)->delete();
        } catch (\Exception $e) {
            Log::error("Database error unlinking Moodle account: " . $e->getMessage());
            return response()->json(['error' => 'Failed to unlink Moodle account in database.'], 500);
        }

        return response()->json(['status' => 'success', 'message' => 'Moodle account unlinked successfully.'], 200);
    }
}
