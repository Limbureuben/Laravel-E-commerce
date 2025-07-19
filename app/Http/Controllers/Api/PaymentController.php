<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    private $baseUrl;
    private $consumerKey;
    private $consumerSecret;

    public function __construct()
    {
        $this->baseUrl = env('PESAPAL_BASE_URL');
        $this->consumerKey = env('PESAPAL_CONSUMER_KEY');
        $this->consumerSecret = env('PESAPAL_CONSUMER_SECRET');
    }

    private function getToken()
    {
        $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
            ->post($this->baseUrl . '/api/Auth/RequestToken');

        return $response->json()['token'] ?? null;
    }

    public function registerIPN()
    {
        \Log::info("Register IPN called");

        try {
            $token = $this->getToken();
            if (!$token) {
                return response()->json(['error' => 'Token not generated'], 500);
            }

            $response = Http::withToken($token)->post($this->baseUrl . '/api/URLSetup/RegisterIPN', [
                'url' => env('APP_URL') . '/api/payment/callback',
                'ipn_notification_type' => 'GET'
            ]);

            return response()->json($response->json());
        } catch (\Exception $e) {
            \Log::error('Error registering IPN: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function callback(Request $request)
    {
        \Log::info('IPN Callback Received', $request->all());
        return response()->json(['message' => 'Callback received']);
    }
}
