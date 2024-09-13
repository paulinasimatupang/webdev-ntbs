<?php

namespace App\Services;

use Google\Auth\Credentials\ServiceAccountCredentials;
use GuzzleHttp\Client;
use App\Entities\User; 

class SendPushNotification
{
    public function sendNotification($userId)
    {
        // Menggunakan model User untuk mendapatkan FCM token
        $user = User::find($userId); // Mengambil user berdasarkan user ID

        if (!$user || !$user->fcm_token) {
            die('FCM token tidak ditemukan untuk user ID ' . $userId);
        }

        $fcmToken = $user->fcm_token; // Mengambil FCM token dari user

        // Path ke file JSON Service Account yang sudah kamu download dari Firebase Console
        $serviceAccountFile = 'firebase/ntbs-lakupandai-firebase-adminsdk-6o46s-e72605460b.json'; // Ganti dengan path ke file JSON kamu

        // Menggunakan Service Account untuk otentikasi
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $credentials = new ServiceAccountCredentials($scopes, $serviceAccountFile);

        // Fetch Access Token dari Service Account
        $token = $credentials->fetchAuthToken()['access_token'];

        // Membuat HTTP client menggunakan Guzzle
        $client = new Client([
            'base_uri' => 'https://fcm.googleapis.com/',
            'headers' => [
                'Authorization' => 'Bearer ' . $token,  // Access Token
                'Content-Type' => 'application/json',
            ],
        ]);

        // Data notifikasi yang ingin dikirim
        $notification = [
            'title' => 'Permohonan Disetujui',
            'body' => 'Pengajuan rekening Anda telah disetujui oleh supervisor.',
        ];

        // Data untuk FCM API request
        $message = [
            'message' => [
                'token' => $fcmToken, // Menggunakan FCM token yang diambil dari database
                'notification' => $notification,
            ],
        ];

        try {
            // Kirim POST request ke FCM API v1
            $response = $client->post('v1/projects/ntbs-lakupandai/messages:send', [
                'json' => $message,
            ]);

            // Menampilkan hasil respons dari Firebase
            echo $response->getBody()->getContents();
        } catch (Exception $e) {
            // Menangani error
            echo 'Error: ' . $e->getMessage();
        }
    }
}
