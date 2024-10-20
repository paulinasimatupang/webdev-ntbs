<?php

namespace App\Services;

use Google_Client;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    protected $client;

    public function __construct()
    {
        // Update path to new service account JSON file
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path('firebase/mobilelakupandaintbs-firebase-adminsdk-cxmgx-2c621fa513.json'));
        $this->client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    }

    public function sendFirebaseNotification($message)
    {
        // Fetch access token from the new service account
        $this->client->fetchAccessTokenWithAssertion();
        $accessToken = $this->client->getAccessToken();
        
        if (!isset($accessToken['access_token'])) {
            Log::error('Failed to retrieve access token');
            return;
        }

        // Update the URL with the new project ID
        $url = 'https://fcm.googleapis.com/v1/projects/mobilelakupandaintbs/messages:send';
        $headers = [
            'Authorization: Bearer ' . $accessToken['access_token'],
            'Content-Type: application/json'
        ];

        // Initialize cURL for sending the notification
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['message' => $message]));
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Activate in production environment
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);  // Timeout after 30 seconds

        // Execute the cURL request
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            Log::error('Curl error: ' . curl_error($ch));
        }

        // Handle non-200 HTTP responses
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            Log::error('FCM send error: HTTP status code ' . curl_getinfo($ch, CURLINFO_HTTP_CODE));
        }

        curl_close($ch);

        // Log the Firebase response
        Log::info('Firebase response: ' . $result);
    }
}
