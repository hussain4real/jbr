<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/test', function() {

    function aesEncryptGCM($plainText, $key)
    {
        // Step ii: Create Initialization Vector (IV) of random 16 bytes
        $iv = random_bytes(16);

        // Step iv: Get Cipher instance of as AES 256 GCM with No Padding (RAW)
        $cipher = 'aes-256-gcm';

        // Encrypt the data (with GCM, you get ciphertext and tag)
        $tag = null;
        $cipherText = openssl_encrypt($plainText, $cipher, $key, OPENSSL_RAW_DATA, $iv, $tag);

        // Step vi: Convert the encrypted value to Hex and incorporate TAG
        $hexCipherText = bin2hex($cipherText);
        $hexTag = bin2hex($tag);

        // Step vii: Convert the IV to Hex
        $hexIv = bin2hex($iv);

        // Step viii: Append the IV Hex value to the Cipher+Tag Hex value
        $encRequest = $hexIv . $hexCipherText . $hexTag;

        return $encRequest;
    }

    $baseUrl = 'https://pguattrans.soharinternational.com:/transaction.do?command=initiateTransaction';
    $merchant_id = env('MERCHANT_ID');
    $order_id = '123456';
    $currency = 'OMR';
    $amount = '1.00';
    $redirect_url = 'http://localhost:8000/redirect';
    $cancel_url = 'http://localhost:8000/cancel';
    $customer_id = '123456';
    $merchant_param1 = 'hello';
    //STEP1: string formation
    $str = $merchant_id.'|'.$order_id.'|'.$currency.'|'.$amount.'|'.$redirect_url.'|'.$cancel_url.'|'.$customer_id.'|'.$merchant_param1;
    //STEP2: String Encryption using AES
    $plaintext = 'Hello world';
    $enc_request = aesEncryptGCM($str, env('WORKING_KEY'));
//    dd($enc_request);
    //STEP3: Base64 encoding
//    $enc_request = base64_encode($enc_request);
//    STEP3: Payment initiation
    $access_code = env('ACCESS_CODE');
    $data = [
        'enc_request' => $enc_request,
//        'access_code' => $access_code,
//        'request_type' => 'JSON',
//        'response_type' => 'JSON',
//        'version' => '1.2'
    ];
//    dd($data);

    try {
        $response = Http::post($baseUrl, $data);
        dd($response->body());
        $response = $response->json();
        $dec_response = decrypt($response['enc_response']);
        $response = json_decode($dec_response, true);
        dd($response);
    } catch (\Exception $e) {
        dd($e->getMessage());
    }



});

require __DIR__.'/auth.php';
