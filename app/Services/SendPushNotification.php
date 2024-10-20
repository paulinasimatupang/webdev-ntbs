<?php

namespace App\Services;

use Google_Client;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use App\Entities\DataCalonNasabah;
use Exception;

class SendPushNotification
{
    // Fungsi untuk mengirim notifikasi berdasarkan token FCM
    public function sendNotificationToToken($fcmToken, $dataPayload)
    {
        try {
            if (empty($fcmToken)) {
                throw new Exception("FCM token is missing.");
            }

            Log::info('Sending notification to FCM token: ' . $fcmToken);

            // Path to the new Firebase Service Account JSON file
            $serviceAccountFile = storage_path('firebase/mobilelakupandaintbs-firebase-adminsdk-cxmgx-2c621fa513.json');

            // Initialize Google Client
            $client = new Google_Client();
            $client->setAuthConfig($serviceAccountFile);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

            // Fetch the access token
            $client->fetchAccessTokenWithAssertion();
            $accessToken = $client->getAccessToken();
            if (!isset($accessToken['access_token'])) {
                throw new Exception('Failed to retrieve access token.');
            }

            Log::info('Access Token: ' . $accessToken['access_token']);

            // Send notification using FCM
            $httpClient = new Client([
                'base_uri' => 'https://fcm.googleapis.com/',
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken['access_token'],
                    'Content-Type' => 'application/json',
                ],
            ]);

            // Gunakan data payload yang dikirimkan melalui parameter
            $message = [
                'message' => [
                    'token' => $fcmToken,
                    'notification' => [
                        'title' => $dataPayload['title'] ?? 'Notifikasi',
                        'body' => $dataPayload['message'] ?? 'Pesan notifikasi',
                    ],
                    'data' => $dataPayload
                ],
            ];

            Log::info('Sending Message: ' . json_encode($message));

            // Send POST request to FCM API with the new project ID
            $response = $httpClient->post('v1/projects/mobilelakupandaintbs/messages:send', [
                'json' => $message,
            ]);

            // Check response status
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();
            if ($statusCode != 200) {
                Log::error('FCM send error: ' . $statusCode . ' - ' . $responseBody);
            }

            Log::info('FCM Response: ' . $responseBody);
            return $responseBody;
        } catch (Exception $e) {
            Log::error('Error sending FCM notification: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Gagal mengirim notifikasi: ' . $e->getMessage(),
            ];
        }
    }
}
