<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    private $baseUrl;
    private $consumerKey;
    private $consumerSecret;
    private $notificationId;

    public function __construct()
    {
        $this->baseUrl = env('PESAPAL_BASE_URL');
        $this->consumerKey = env('PESAPAL_CONSUMER_KEY');
        $this->consumerSecret = env('PESAPAL_CONSUMER_SECRET');
        $this->notificationId = env('PESAPAL_NOTIFICATION_ID');
    }

    private function getToken()
    {
        $response = Http::post("{$this->baseUrl}/api/Auth/RequestToken", [
            'consumer_key' => $this->consumerKey,
            'consumer_secret' => $this->consumerSecret,
        ]);

        if ($response->successful()) {
            return $response->json()['token'];
        }

        abort(500, 'Failed to get PesaPal token');
    }

    public function initiate(Request $request)
    {
        $request->validate([
        'phone' => 'required|string',
        'amount' => 'required|numeric',
    ]);

    $token = $this->getToken();

    $payload = [
        "id" => uniqid('order_'),
        "currency" => "KES",
        "amount" => $request->amount,
        "description" => "Payment via phone number only",
        "callback_url" => route('pesapal.callback', [], true),
        "notification_id" => $this->notificationId,
        "billing_address" => [
        "email_address" => $request->phone . "@noemail.com",  // dummy email since email is required
        "phone_number" => $request->phone,
        "first_name" => "Customer",
        "last_name" => "MobilePay"
        ]
    ];

    $response = Http::withToken($token)
        ->post("{$this->baseUrl}/api/Transactions/SubmitOrderRequest", $payload);

    if ($response->successful()) {
        return response()->json($response->json());
    }

    return response()->json(['error' => 'Failed to submit order'], 500);
    }
}
