<?php
// app/Services/MoodleApiService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service class to interact with the Moodle Web Services API.
 * This class abstracts the direct HTTP calls to Moodle.
 */
class MoodleApiService
{
    protected string $baseUrl;
    protected string $mobileServiceShortname;

    public function __construct()
    {
        // Retrieve Moodle base URL and service shortname from environment variables
        $this->baseUrl = env('MOODLE_BASE_URL');
        $this->mobileServiceShortname = env('MOODLE_MOBILE_SERVICE_SHORTNAME');

        // Basic validation for Moodle URL
        if (empty($this->baseUrl)) {
            throw new \Exception("MOODLE_BASE_URL is not set in the environment variables.");
        }
    }

    /**
     * Obtains an authentication token from Moodle using user credentials.
     *
     * @param string $username Moodle username.
     * @param string $password Moodle password.
     * @return string|null The Moodle token on success, or null on failure.
     */
    public function getMoodleToken(string $username, string $password): ?string
    {
        $url = "{$this->baseUrl}/login/token.php";
        try {
            // Make a POST request to Moodle's token endpoint
            $response = Http::asForm()->post($url, [
                'username' => $username,
                'password' => $password,
                'service' => $this->mobileServiceShortname,
            ]);

            // Throw an exception if a client or server error occurred (4xx or 5xx response)
            $response->throw();

            $data = $response->json();

            // Check if the 'token' key exists in the response
            if (isset($data['token'])) {
                return $data['token'];
            }

            // Log Moodle's specific error if available
            Log::error("Moodle token request failed: " . ($data['error'] ?? 'Unknown error from Moodle.'));
            return null;

        } catch (\Illuminate\Http\Client\RequestException $e) {
            // Handle HTTP client errors (e.g., network issues, Moodle server down)
            Log::error("HTTP error fetching Moodle token: " . $e->getMessage() . " Response: " . $e->response->body());
            return null;
        } catch (\Exception $e) {
            // Catch any other exceptions during the process
            Log::error("General error fetching Moodle token: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Makes a generic call to the Moodle Web Services API.
     *
     * @param string $token The Moodle authentication token.
     * @param string $wsfunction The Moodle Web Service function name (e.g., 'core_user_get_users').
     * @param array $params Additional parameters for the specific Moodle function.
     * @return array|null The JSON decoded response from Moodle, or null on failure.
     */
    public function callMoodleApi(string $token, string $wsfunction, array $params = []): ?array
    {
        $url = "{$this->baseUrl}/webservice/rest/server.php";
        $payload = [
            'wstoken' => $token,
            'wsfunction' => $wsfunction,
            'moodlewsrestformat' => 'json', // Always request JSON format
        ];
        // Merge additional parameters provided by the caller
        $payload = array_merge($payload, $params);

        try {
            // Make a POST request to the Moodle Web Services endpoint
            $response = Http::asForm()->post($url, $payload);

            // Throw an exception for 4xx/5xx responses
            $response->throw();

            return $response->json();

        } catch (\Illuminate\Http\Client\RequestException $e) {
            // Log the full response body for debugging Moodle API errors
            Log::error("Moodle API call '{$wsfunction}' HTTP error: " . $e->getMessage() . " Response: " . $e->response->body());
            return ['error' => 'Moodle API HTTP error: ' . $e->getMessage(), 'moodle_error_details' => $e->response->json()];
        } catch (\Exception $e) {
            // Catch any other exceptions
            Log::error("General error during Moodle API call '{$wsfunction}': " . $e->getMessage());
            return ['error' => 'An unexpected error occurred during Moodle API call: ' . $e->getMessage()];
        }
    }
} 