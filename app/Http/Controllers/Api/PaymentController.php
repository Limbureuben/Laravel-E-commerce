<?php

namespace App\Http\Controllers\Api;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
   public function callback(Request $request)
    {
        $trackingId = $request->query('orderTrackingId');

        if (!$trackingId) {
            return response()->json(['error' => 'Missing orderTrackingId'], 400);
        }

        $token = $this->getToken();

        $response = Http::withToken($token)
            ->get("{$this->baseUrl}/api/Transactions/GetTransactionStatus", [
                'orderTrackingId' => $trackingId
            ]);

        if ($response->successful()) {
            $status = $response->json();
            return response()->json(['status' => $status]);
        }

        return response()->json(['error' => 'Failed to get transaction status'], 500);
    }

}
