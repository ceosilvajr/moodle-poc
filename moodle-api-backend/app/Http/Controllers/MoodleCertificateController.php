<?php
// app/Http/Controllers/MoodleCertificateController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MoodleApiService;
use App\Models\UserMoodleToken;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * Handles Moodle certificate-related API requests.
 */
class MoodleCertificateController extends Controller
{
    protected MoodleApiService $moodleApiService;

    public function __construct(MoodleApiService $moodleApiService)
    {
        $this->moodleApiService = $moodleApiService;
    }

    /**
     * Helper method to retrieve Moodle token and user ID for the authenticated mobile user.
     *
     * @param string $mobileUserId The ID of the authenticated mobile app user.
     * @return array|null An array containing 'token' and 'moodle_user_id', or null if not linked.
     */
    protected function getMoodleTokenAndId(string $mobileUserId): ?array
    {
        $tokenData = UserMoodleToken::where('mobile_user_id', $mobileUserId)->first();
        if ($tokenData) {
            return ['token' => $tokenData->moodle_token, 'moodle_user_id' => $tokenData->moodle_user_id];
        }
        return null;
    }

    /**
     * Fetches all available certificates for the linked Moodle user.
     * This process is complex due to varying Moodle certificate implementations.
     *
     * @param Request $request The incoming HTTP request.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserCertificates(Request $request)
    {
        $mobileUserId = Auth::id();
        if (!$mobileUserId) {
            return response()->json(['error' => 'Unauthorized: Mobile user not authenticated with backend.'], 401);
        }

        $moodleAuth = $this->getMoodleTokenAndId($mobileUserId);
        if (!$moodleAuth) {
            return response()->json(['error' => 'Moodle account not linked for this user.'], 400);
        }

        $moodleToken = $moodleAuth['token'];
        $moodleUserId = $moodleAuth['moodle_user_id'];

        $userCertificates = [];

        // 1. Get user's enrolled courses
        $enrolledCoursesResponse = $this->moodleApiService->callMoodleApi(
            $moodleToken,
            'core_enrol_get_users_courses',
            ['userid' => $moodleUserId]
        );

        if (isset($enrolledCoursesResponse['error'])) {
            return response()->json(['error' => 'Failed to fetch enrolled courses for certificate search.', 'details' => $enrolledCoursesResponse['error']], 500);
        }

        foreach ($enrolledCoursesResponse as $course) {
            $courseId = $course['id'] ?? null;
            if ($courseId) {
                // 2. Get course contents (modules/activities) to find certificate activities
                $courseContentsResponse = $this->moodleApiService->callMoodleApi(
                    $moodleToken,
                    'core_course_get_contents',
                    ['courseid' => $courseId]
                );

                if (isset($courseContentsResponse['error'])) {
                    Log::warning("Could not get contents for course {$courseId} during certificate check: " . ($courseContentsResponse['error'] ?? 'Unknown error'));
                    continue; // Skip to next course if contents can't be fetched
                }

                // Iterate through sections and modules to find certificate activities
                foreach ($courseContentsResponse as $section) {
                    foreach ($section['modules'] ?? [] as $module) {
                        // Check if the module is a certificate type (e.g., 'certificate' for built-in, 'customcert' for plugin)
                        if (in_array($module['modname'] ?? '', ['certificate', 'customcert'])) { // Adjust 'modname' as per your Moodle's certificate plugin
                            // 3. Check if the certificate activity is completed for the user
                            $completionStatus = $this->moodleApiService->callMoodleApi(
                                $moodleToken,
                                'core_completion_get_activities_completion_status',
                                ['courseid' => $courseId, 'userid' => $moodleUserId, 'cmid' => $module['id']]
                            );

                            // Moodle's completion status: state = 1 usually means completed
                            if (!isset($completionStatus['error']) && isset($completionStatus['statuses'][0]) && $completionStatus['statuses'][0]['state'] == 1) {
                                $certificateData = [
                                    "id" => $module['id'],
                                    "name" => $module['name'],
                                    "course_id" => $courseId,
                                    "course_name" => $course['fullname'] ?? 'N/A',
                                    "status" => "completed",
                                    "issue_date" => null, // Moodle API doesn't always provide issue date directly here
                                ];

                                // 4. Attempt to construct a download URL for the certificate PDF
                                // This is highly dependent on the specific certificate plugin and Moodle configuration.
                                // Moodle often uses pluginfile.php for file serving.
                                if (isset($module['contents'])) {
                                    foreach ($module['contents'] as $content) {
                                        // Look for a file URL that contains 'certificate' and is a PDF
                                        if (isset($content['fileurl']) && strpos($content['fileurl'], 'certificate') !== false && strpos($content['fileurl'], '.pdf') !== false) {
                                            // Append the Moodle token to the file URL for authenticated download
                                            $certificateData['download_url'] = "{$content['fileurl']}&token={$moodleToken}";
                                            break;
                                        }
                                    }
                                }
                                $userCertificates[] = $certificateData;
                            }
                        }
                    }
                }
            }
        }

        return response()->json($userCertificates, 200);
    }
}
