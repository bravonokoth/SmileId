<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log; // Ensure Log facade is imported

class KraPinVerificationController extends Controller
{
    protected $partnerId;
    protected $apiKey;

    public function __construct()
    {
        $this->partnerId = "7158"; // Ensure these are set in your .env file
        $this->apiKey = "2ab5f434-f8a9-4897-b115-86e58eb300ab"; 
    }

    public function generateSignature()
    {
        // Create a timestamp in ISO 8601 format with milliseconds
        $timestamp = (new \DateTime())->format("Y-m-d\TH:i:s.vP"); 

        // Construct the message for signature
        $message = $timestamp . $this->partnerId . "sid_request";

        // Generate the HMAC SHA-256 hash and encode it in base64
        $signature = base64_encode(hash_hmac('sha256', $message, $this->apiKey, true));

        return [
            'signature' => $signature,
            'timestamp' => $timestamp
        ];
    }

    public function verify(Request $request)
    {
        // Validate the request to ensure the KRA PIN is provided
        $request->validate([
            'kra_pin' => 'required|string|max:10', // Validate KRA PIN
        ]);

        // Generate the signature
        $signatureData = $this->generateSignature();

        // Prepare the request body
        $requestBody = [
            'country' => 'KE', // Kenya
            'id_type' => 'KRA_PIN', // ID type set to KRA_PIN
            'id_number' => $request->kra_pin, // Use the provided KRA PIN as the ID number
            'citizenship' => 'Kenyan', // Citizenship can be set as needed
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

        // Log the request body for debugging
        Log::info('KRA PIN Verification Request:', $requestBody);

        // Make the API request to the correct Smile Identity Test API endpoint
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey, // Include the Authorization header
            ])->post('https://testapi.smileidentity.com/v1/id_verification', $requestBody); // Corrected API URL

            // Log the raw response
            Log::info('KRA PIN Verification Response:', ['status' => $response->status(), 'body' => $response->body()]);

            // Parse the response
            $responseData = $response->json();

            // Decode the 'body' field if it's a JSON string
            if (isset($responseData['body'])) {
                $responseBody = json_decode($responseData['body'], true);
            } else {
                $responseBody = $responseData;
            }

            // Extract the 'business_name'
            $businessName = $responseBody['business_name'] ?? 'None';

            // Extract the 'success' flag from 'FullData'
            $success = $responseBody['FullData']['success'] ?? false;

            // Log the extracted data for debugging
            Log::info('Parsed KRA PIN Verification Response:', [
                'business_name' => $businessName,
                'success' => $success,
            ]);

            // Check if the verification was successful
            if ($success) {
                // Return the view with the response data
                return view('kra_verification')->with([
                    'response' => $responseBody,
                    'business_name' => $businessName,
                    'success' => $success,
                ]);
            } else {
                // If the verification failed, capture error details
                $errorMessage = $responseBody['ResultText'] ?? $responseBody['message'] ?? 'Unknown error';
                $errorCode = $responseBody['ResultCode'] ?? 'N/A';

                // Log the error for debugging
                Log::error('KRA PIN Verification Failed:', [
                    'message' => $errorMessage,
                    'code' => $errorCode,
                ]);

                // Return the view with error data
                return view('kra_verification')->with([
                    'response' => $responseBody,
                    'business_name' => $businessName,
                    'success' => $success,
                    'error_message' => $errorMessage,
                    'error_code' => $errorCode,
                ]);
            }
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('KRA PIN Verification Exception:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return the view with a generic error message
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
