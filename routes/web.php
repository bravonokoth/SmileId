<?php

use App\Http\Controllers\KycController;
use App\Http\Controllers\KraPinVerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('basic_kyc');
});

Route::get('/generate-signature', [KycController::class, 'generateSignature']);
Route::post('/basic-kyc', [KycController::class, 'callBasicKycApi']);


// Define a route for the KRA PIN verification form
Route::get('/kra-verification', function () {
    return view('kra_verification'); 
});

// Define a route to handle the verification request
Route::post('/verify-kra-pin', [KraPinVerificationController::class, 'verify'])->name('verifyKraPin');
