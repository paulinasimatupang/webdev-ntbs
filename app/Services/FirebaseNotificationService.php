<?php

namespace App\Services;

use Google_Client;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path('firebase/ntbs-lakupa-firebase-adminsdk-w89ha-b762e4a1fd.json'));
        $this->client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    }

    public function sendFirebaseNotification($message)
    {
        $this->client->fetchAccessTokenWithAssertion();
        $accessToken = $this->client->getAccessToken();
        
        if (!isset($accessToken['access_token'])) {
            Log::error('Failed to retrieve access token');
            return;
        }

        $url = 'https://fcm.googleapis.com/v1/projects/ntbs-lakupandai/messages:send';
        $headers = [
            'Authorization: Bearer ' . $accessToken['access_token'],
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['message' => $message]));
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Ingat untuk mengaktifkan ini di lingkungan produksi
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);  // Timeout setelah 30 detik

        // Eksekusi cURL
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            Log::error('Curl error: ' . curl_error($ch));
        }

        // Menangani respons HTTP yang bukan 200 OK
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            Log::error('FCM send error: HTTP status code ' . curl_getinfo($ch, CURLINFO_HTTP_CODE));
        }

        curl_close($ch);

        // Log respons Firebase
        Log::info('Firebase response: ' . $result);
    }
}
