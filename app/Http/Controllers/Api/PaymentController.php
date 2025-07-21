<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// class PaymentController extends Controller
// {
//     private $baseUrl;
//     private $consumerKey;
//     private $consumerSecret;

//     public function __construct()
//     {
//         $this->baseUrl = env('PESAPAL_BASE_URL');
//         $this->consumerKey = env('PESAPAL_CONSUMER_KEY');
//         $this->consumerSecret = env('PESAPAL_CONSUMER_SECRET');
//     }

//    private function getToken()
//     {
//         try {
//             $response = Http::asJson()
//                 ->withHeaders([
//                     'Accept' => 'application/json',
//                 ])
//                 ->post($this->baseUrl . '/api/Auth/RequestToken', [
//                     'consumer_key' => $this->consumerKey,
//                     'consumer_secret' => $this->consumerSecret,
//                 ]);

//             \Log::info('Pesapal Token Response', [
//                 'status' => $response->status(),
//                 'body' => $response->body(),
//             ]);

//             if ($response->successful()) {
//                 $json = $response->json();
//                 return $json['token'] ?? null;
//             }

//         } catch (\Exception $e) {
//             \Log::error('Pesapal Token Error', [
//                 'message' => $e->getMessage(),
//             ]);
//         }

//         return null;
//     }




//     public function registerIPN()
//     {
//         \Log::info("Register IPN called");

//         try {
//             $token = $this->getToken();

//             if (!$token) {
//                 return response()->json(['error' => 'Token not generated'], 500);
//             }

//             $response = Http::withToken($token)->post($this->baseUrl . '/api/URLSetup/RegisterIPN', [
//                 'url' => env('APP_URL') . '/api/payment/callback',
//                 'ipn_notification_type' => 0
//             ]);

//             if ($response->successful()) {
//                 return response()->json($response->json());
//             }

//             \Log::error('IPN registration failed: ' . $response->body());
//             return response()->json(['error' => 'Failed to register IPN', 'details' => $response->body()], 500);
//         } catch (\Exception $e) {
//             \Log::error('Exception during IPN registration: ' . $e->getMessage());
//             return response()->json(['error' => $e->getMessage()], 500);
//         }
//     }



//     public function testToken()
//     {
//         $response = Http::withHeaders([
//             'Accept' => 'application/json',
//         ])->post($this->baseUrl . '/api/Auth/RequestToken', [
//             'consumer_key' => $this->consumerKey,
//             'consumer_secret' => $this->consumerSecret,
//         ]);

//         return response()->json([
//             'status' => $response->status(),
//             'body' => $response->body(),
//             'json' => $response->json(),
//         ]);
//     }


//     public function submitOrder(Request $request)
//     {
//         $request->validate([
//             'phone' => 'required|string',
//             'amount' => 'required|numeric|min:100',
//         ]);

//         $token = $this->getToken();
//         if (!$token) {
//             return response()->json(['error' => 'Token not generated'], 500);
//         }

//         $ipnId = env('PESAPAL_IPN_ID'); // Save this after IPN registration

//         $orderData = [
//             'amount' => $request->input('amount'),
//             'currency' => 'TZS',
//             'description' => 'Payment via Mobile Money',
//             'ipn_id' => $ipnId,
//             'merchant_reference' => uniqid('order_'),
//             'callback_url' => env('APP_URL') . '/api/ipn-handler',
//             'billing_address' => [
//                 'email' => 'limbureubenn@gmail.com',
//                 'phone_number' => $request->input('phone'),
//                 'first_name' => 'Customer',
//                 'last_name' => 'User',
//                 'line_1' => 'N/A',
//                 'city' => 'N/A',
//                 'state' => 'N/A',
//                 'country_code' => 'TZ',
//                 'postal_code' => '00000'
//             ]
//         ];

//         $response = Http::withToken($token)
//         ->post($this->baseUrl . '/api/Transactions/SubmitOrderRequest', $orderData);

//         if ($response->successful()) {
//             return response()->json($response->json());
//         }

//         return response()->json([
//         'error' => 'Failed to submit order',
//         'details' => $response->body()
//     ], 500);
//     }


//     public function callback(Request $request)
//     {
//         \Log::info('IPN Callback Received', $request->all());
//         return response()->json(['message' => 'Callback received']);
//     }
// }






class PaymentController extends Controller
{
    private $authUrl;
    private $orderUrl;
    private $consumerKey;
    private $consumerSecret;

    public function __construct()
    {
        $this->authUrl = env('PESAPAL_AUTH_URL');       // e.g. https://cybqa.pesapal.com
        $this->orderUrl = env('PESAPAL_ORDER_URL');     // e.g. https://cybqa.pesapal.com/pesapalv3
        $this->consumerKey = env('PESAPAL_CONSUMER_KEY');
        $this->consumerSecret = env('PESAPAL_CONSUMER_SECRET');
    }

    private function getToken()
    {
        try {
            $response = Http::asJson()
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->post($this->authUrl . '/api/Auth/RequestToken', [
                    'consumer_key' => $this->consumerKey,
                    'consumer_secret' => $this->consumerSecret,
                ]);

            \Log::info('Pesapal Token Response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $json = $response->json();
                return $json['token'] ?? null;
            }

        } catch (\Exception $e) {
            \Log::error('Pesapal Token Error', [
                'message' => $e->getMessage(),
            ]);
        }

        return null;
    }

    public function registerIPN()
    {
        \Log::info("Register IPN called");

        try {
            $token = $this->getToken();

            if (!$token) {
                return response()->json(['error' => 'Token not generated'], 500);
            }

            $response = Http::withToken($token)->post($this->orderUrl . '/api/URLSetup/RegisterIPN', [
                'url' => env('APP_URL') . '/api/payment/callback',
                'ipn_notification_type' => 0
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            \Log::error('IPN registration failed: ' . $response->body());
            return response()->json(['error' => 'Failed to register IPN', 'details' => $response->body()], 500);
        } catch (\Exception $e) {
            \Log::error('Exception during IPN registration: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function testToken()
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post($this->authUrl . '/api/Auth/RequestToken', [
            'consumer_key' => $this->consumerKey,
            'consumer_secret' => $this->consumerSecret,
        ]);

        return response()->json([
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $response->json(),
        ]);
    }

    public function submitOrder(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'amount' => 'required|numeric|min:100',
        ]);

        $token = $this->getToken();
        if (!$token) {
            return response()->json(['error' => 'Token not generated'], 500);
        }

        $ipnId = env('PESAPAL_IPN_ID'); // Should be saved after IPN registration

        $orderData = [
            'amount' => $request->input('amount'),
            'currency' => 'TZS',
            'description' => 'Payment via Mobile Money',
            'ipn_id' => $ipnId,
            'merchant_reference' => uniqid('order_'),
            'callback_url' => env('APP_URL') . '/api/ipn-handler',
            'billing_address' => [
                'email' => 'limbureubenn@gmail.com',
                'phone_number' => $request->input('phone'),
                'first_name' => 'Customer',
                'last_name' => 'User',
                'line_1' => 'N/A',
                'city' => 'N/A',
                'state' => 'N/A',
                'country_code' => 'TZ',
                'postal_code' => '00000'
            ]
        ];

        $response = Http::withToken($token)
            ->post($this->orderUrl . '/api/Transactions/SubmitOrderRequest', $orderData);

        if ($response->successful()) {
            return response()->json($response->json());
        }

        return response()->json([
            'error' => 'Failed to submit order',
            'details' => $response->body()
        ], 500);
    }

    public function callback(Request $request)
    {
        \Log::info('IPN Callback Received', $request->all());
        return response()->json(['message' => 'Callback received']);
    }
}
