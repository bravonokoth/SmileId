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
        $this->partnerId = "7158"; 
        $this->apiKey = "2ab5f434-f8a9-4897-b115-86e58eb300ab"; 
    }

    public function generateSignature()
    {
        
        $timestamp = (new \DateTime())->format("Y-m-d\TH:i:s.vP"); // ISO 8601 format with milliseconds
    
        
        $message = $timestamp . $this->partnerId . "sid_request";
    
       
        $signature = base64_encode(hash_hmac('sha256', $message, $this->apiKey, true));
    
        
        return [
            'signature' => $signature,
            'timestamp' => $timestamp
        ];
    }
    


    public function callBasicKycApi(Request $request)
    {
        
        $request->validate([
            'id_number' => 'required|regex:/^[0-9]{1,9}$/', 
        ]);

        
        $signatureData = $this->generateSignature();

        
        $requestBody = [
            'country' => 'KE', // Kenya
            'id_type' => 'KRA_PIN', // ID type
            'id_number' => $request->id_number, // ID number from user input
            'citizenship' => 'Kenyan', 
            'partner_id' => $this->partnerId,
            'signature' => $signatureData['signature'],
            'timestamp' => $signatureData['timestamp'],
            'source_sdk' => 'rest_api',
            'source_sdk_version' => '1.0.0',
            'partner_params' => [
                'job_id' => $request->job_id ?? '', 
                'user_id' => $request->user_id ?? '', 
            ],
        ];

       
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post('https://testapi.smileidentity.com/v1/id_verification', $requestBody);

        
        return view('basic_kyc')->with('response', $response->json());
    }
}

