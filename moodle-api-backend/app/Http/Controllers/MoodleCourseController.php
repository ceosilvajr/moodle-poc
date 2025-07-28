<?php
// app/Http/Controllers/MoodleCourseController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MoodleApiService;
use App\Models\UserMoodleToken;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * Handles Moodle course-related API requests.
 */
class MoodleCourseController extends Controller
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
     * Fetches courses the linked Moodle user is enrolled in, categorized by status.
     *
     * @param Request $request The incoming HTTP request.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEnrolledCourses(Request $request)
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

        // 1. Get user's enrolled courses from Moodle
        $enrolledCoursesResponse = $this->moodleApiService->callMoodleApi(
            $moodleToken,
            'core_enrol_get_users_courses',
            ['userid' => $moodleUserId]
        );

        if (isset($enrolledCoursesResponse['error'])) {
            return response()->json(['error' => 'Failed to fetch enrolled courses from Moodle.', 'details' => $enrolledCoursesResponse['error']], 500);
        }

        $coursesWithStatus = [];
        foreach ($enrolledCoursesResponse as $course) {
            $courseId = $course['id'] ?? null;
            if ($courseId) {
                // 2. Get completion status for each course
                $completionStatusResponse = $this->moodleApiService->callMoodleApi(
                    $moodleToken,
                    'core_completion_get_course_completion_status',
                    ['courseid' => $courseId, 'userid' => $moodleUserId]
                );

                if (isset($completionStatusResponse['error'])) {
                    // Log the error but continue processing other courses
                    Log::warning("Could not get completion status for course {$courseId}: " . ($completionStatusResponse['error'] ?? 'Unknown error'));
                    $course['status'] = 'unknown'; // Assign an unknown status if API call fails
                } else {
                    // Moodle's 'iscomplete' field indicates completion
                    $course['status'] = ($completionStatusResponse['iscomplete'] ?? false) ? 'completed' : 'in-progress';
                }
                $coursesWithStatus[] = $course;
            }
        }

        return response()->json($coursesWithStatus, 200);
    }

    /**
     * Fetches all courses available on the Moodle site that the linked user has permission to browse.
     * If the user doesn't have permission to view all courses, returns their enrolled courses.
     *
     * @param Request $request The incoming HTTP request.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableCourses(Request $request)
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

        // First, try to get all courses from Moodle (user's permissions apply)
        $allCoursesResponse = $this->moodleApiService->callMoodleApi(
            $moodleToken,
            'core_course_get_courses'
        );

        // Check if the response contains an error (either as 'error' key or as exception in the response)
        $hasError = isset($allCoursesResponse['error']) || 
                   (isset($allCoursesResponse['exception']) && isset($allCoursesResponse['errorcode']));
        
        // If we can get all courses, return them
        if (!$hasError) {
            return response()->json([
                'courses' => $allCoursesResponse,
                'message' => 'All available courses retrieved successfully.',
                'permission_level' => 'full_access'
            ], 200);
        }

        // If we get a permission error, fall back to enrolled courses
        $errorMessage = $allCoursesResponse['error'] ?? $allCoursesResponse['message'] ?? '';
        if ($hasError && 
            (strpos($errorMessage, 'nopermissions') !== false || 
             strpos($errorMessage, 'required_capability_exception') !== false ||
             strpos($errorMessage, 'View courses without participation') !== false)) {
            
            // Get user's enrolled courses as fallback
            $enrolledCoursesResponse = $this->moodleApiService->callMoodleApi(
                $moodleToken,
                'core_enrol_get_users_courses',
                ['userid' => $moodleUserId]
            );

            if (isset($enrolledCoursesResponse['error'])) {
                return response()->json([
                    'error' => 'Failed to fetch courses from Moodle.',
                    'details' => $enrolledCoursesResponse['error']
                ], 500);
            }

            return response()->json([
                'courses' => $enrolledCoursesResponse,
                'message' => 'Limited access: Only enrolled courses are available. Contact your Moodle administrator for full course access.',
                'permission_level' => 'enrolled_only',
                'note' => 'Your Moodle account does not have permission to view all courses. Only your enrolled courses are shown.'
            ], 200);
        }

        // For other types of errors, return the original error
        return response()->json([
            'error' => 'Failed to fetch available courses from Moodle.',
            'details' => $allCoursesResponse['error']
        ], 500);
    }

    /**
     * Attempts to enroll the linked Moodle user in a specific course.
     *
     * @param Request $request The incoming HTTP request.
     * @param int $courseId The ID of the course to enroll in.
     * @return \Illuminate\Http\JsonResponse
     */
    public function enrollUserInCourse(Request $request, int $courseId)
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

        // Call Moodle's self-enrollment function.
        // This only works if the course is configured for self-enrollment.
        // Otherwise, Moodle will return an error indicating the enrollment method is not valid.
        $enrollResponse = $this->moodleApiService->callMoodleApi(
            $moodleToken,
            'enrol_self_enrol_user',
            ['courseid' => $courseId, 'userid' => $moodleUserId]
        );

        if (isset($enrollResponse['error'])) {
            return response()->json(['error' => 'Failed to enroll user in course.', 'details' => $enrollResponse['error']], 500);
        }
        
        // Moodle's self-enrollment function typically returns an empty object or a success status on success.
        return response()->json(['status' => 'success', 'message' => "User {$moodleUserId} enrolled in course {$courseId} successfully."], 200);
    }
}
