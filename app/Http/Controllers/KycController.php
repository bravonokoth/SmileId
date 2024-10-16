<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use SmileIdentity\Signature;

class KycController extends Controller
{
    protected $partnerId;
    protected $apiKey;

    public function __construct()
    {
        $this->partnerId = "7158"; // Store in your .env file
        $this->apiKey = "2ab5f434-f8a9-4897-b115-86e58eb300ab"; 
    }

    public function generateSignature()
    {
        // Create a timestamp in the required format
        $timestamp = (new \DateTime())->format("Y-m-d\TH:i:s.vP"); // ISO 8601 format with milliseconds
    
        // Construct the message
        $message = $timestamp . $this->partnerId . "sid_request";
    
        // Generate the HMAC SHA-256 hash
        $signature = base64_encode(hash_hmac('sha256', $message, $this->apiKey, true));
    
        // Return both the signature and the timestamp
        return [
            'signature' => $signature,
            'timestamp' => $timestamp
        ];
    }
    


    public function callBasicKycApi(Request $request)
    {
        // Validate the request to ensure the ID number is provided
        $request->validate([
            'id_number' => 'required|regex:/^[0-9]{1,9}$/', // Ensure the ID number follows the regex
        ]);

        // Generate the signature
        $signatureData = $this->generateSignature();

        // Prepare the request body
        $requestBody = [
            'country' => 'KE', // Kenya
            'id_type' => 'KRA_PIN', // ID type
            'id_number' => $request->id_number, // ID number from user input
            'citizenship' => 'Kenyan', // You can change this as needed
            'partner_id' => $this->partnerId,
            'signature' => $signatureData['signature'],
            'timestamp' => $signatureData['timestamp'],
            'source_sdk' => 'rest_api',
            'source_sdk_version' => '1.0.0',
            'partner_params' => [
                'job_id' => $request->job_id ?? '', // Optional, handle gracefully
                'user_id' => $request->user_id ?? '', // Optional, handle gracefully
            ],
        ];

        // Make the API request
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://testapi.smileidentity.com/v1/id_verification', $requestBody);

        // Return back to the form with the response
        return view('basic_kyc')->with('response', $response->json());
    }
}

