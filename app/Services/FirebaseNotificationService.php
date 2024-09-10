<?php

namespace App\Services;

use Google_Client;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Client;

class FirebaseNotificationService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setAuthConfig(storage_path('firebase/ntbs-lakupandai-firebase-adminsdk-6o46s-e72605460b.json'));
        $this->client->addScope('https://www.googleapis.com/auth/firebase.messaging');
    }

    public function sendFirebaseNotification($message)
    {
        $this->client->fetchAccessTokenWithAssertion();
        $accessToken = $this->client->getAccessToken()['access_token'];

        $url = 'https://fcm.googleapis.com/v1/projects/ntbs-lakupandai/messages:send';
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['message' => $message]));

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            Log::error('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);

        Log::info('Firebase response: ' . $result);
    }
}
