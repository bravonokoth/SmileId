<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; 

class KraPinVerificationController extends Controller
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
        // Timestamp in ISO 8601 format with milliseconds
        $timestamp = (new \DateTime())->format("Y-m-d\TH:i:s.vP"); 

        // the message for signature
        $message = $timestamp . $this->partnerId . "sid_request";

        // The HMAC SHA-256 hash and encode it in base64
        $signature = base64_encode(hash_hmac('sha256', $message, $this->apiKey, true));

        return [
            'signature' => $signature,
            'timestamp' => $timestamp
        ];
    }

    public function verify(Request $request)
    {
        
        $request->validate([
            'kra_pin' => 'required|string|max:10', 
        ]);

        
        $signatureData = $this->generateSignature();

        
        $requestBody = [
            'country' => 'KE', 
            'id_type' => 'KRA_PIN', 
            'id_number' => $request->kra_pin, 
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

        
        Log::info('KRA PIN Verification Request:', $requestBody);

       
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey, 
            ])->post('https://testapi.smileidentity.com/v1/id_verification', $requestBody); 

           
            Log::info('KRA PIN Verification Response:', ['status' => $response->status(), 'body' => $response->body()]);

            
            $responseData = $response->json();

            
            if (isset($responseData['body'])) {
                $responseBody = json_decode($responseData['body'], true);
            } else {
                $responseBody = $responseData;
            }

            // Extract the 'business_name'
            $businessName = $responseBody['business_name'] ?? 'None';

            // Extract the 'success' flag from 'FullData'
            $success = $responseBody['FullData']['success'] ?? false;

            
            Log::info('Parsed KRA PIN Verification Response:', [
                'business_name' => $businessName,
                'success' => $success,
            ]);

            
            if ($success) {
                
                return view('kra_verification')->with([
                    'response' => $responseBody,
                    'business_name' => $businessName,
                    'success' => $success,
                ]);
            } else {
               
                $errorMessage = $responseBody['ResultText'] ?? $responseBody['message'] ?? 'Unknown error';
                $errorCode = $responseBody['ResultCode'] ?? 'N/A';

               
                Log::error('KRA PIN Verification Failed:', [
                    'message' => $errorMessage,
                    'code' => $errorCode,
                ]);

                
                return view('kra_verification')->with([
                    'response' => $responseBody,
                    'business_name' => $businessName,
                    'success' => $success,
                    'error_message' => $errorMessage,
                    'error_code' => $errorCode,
                ]);
            }
        } catch (\Exception $e) {
            
            Log::error('KRA PIN Verification Exception:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            
            return view('kra_verification')->with([
                'response' => null,
                'business_name' => 'None',
                'success' => false,
                'error_message' => 'An unexpected error occurred. (Code: N/A)',
                'error_code' => 'N/A',
            ]);
        }
    }
}
