<?php

namespace App\Http\Controllers;

use App\services\CryptoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Random\RandomException;

class PaymentController extends Controller
{
    protected $cryptoService;

    protected $merchantId;
    protected $accessCode;
    public function __construct(CryptoService $cryptoService)
    {
        $this->cryptoService = $cryptoService;
        $this->merchantId = env('MERCHANT_ID');
        $this->accessCode = env('ACCESS_CODE');
    }

    public function showForm()
    {
       return Inertia::render('Payment/PaymentForm',[
              'merchantId' => $this->merchantId,
              'accessCode' => $this->accessCode,
               'orderId' => 'ORD' . rand(1000, 9999),
               'redirectUrl' => route('payment-response'),
              'cancelUrl' => route('payment.cancel')
       ]);
    }

    /**
     * @throws RandomException
     */
    public function initiateTransaction(Request $request)
    {
//        dd($request->all());
        $data = $request->all();
        $merchantData = '';

        foreach ($data as $key => $value) {
            $merchantData .= $key . '=' . urlencode($value) . '&';
        }

        $encryptedData = $this->cryptoService->encrypt($merchantData);

//        dd($encryptedData);

        return Inertia::render('Payment/PaymentRedirect',[
            'encRequest' => $encryptedData,
            'accessCode' => env('ACCESS_CODE'),
            'actionUrl' => env('ACTION_URL'),
        ]);

    }

    public function handleResponse(Request $request)
    {
        // Retrieve and decrypt the encrypted response
        $encResponse = $request->input('encResp');
        $decryptedResponse = $this->cryptoService->decrypt($encResponse);

        // Parse the decrypted response into an associative array
        $responseParams = [];
        parse_str($decryptedResponse, $responseParams);

        // Extract important parameters
        $orderId = $responseParams['order_id'] ?? null;
        $trackingId = $responseParams['tracking_id'] ?? null;
        $orderStatus = $responseParams['order_status'] ?? null;
        $paymentMode = $responseParams['payment_mode'] ?? null;
        $amount = $responseParams['amount'] ?? null;
        $billingName = $responseParams['billing_name'] ?? null;
        $billingEmail = $responseParams['billing_email'] ?? null;

        // Check the order status
        if ($orderStatus === 'Success') {
            // Handle success
            // Example: Storing the payment information in the database
            // Payment::create([
            //     'order_id' => $orderId,
            //     'tracking_id' => $trackingId,
            //     'order_status' => $orderStatus,
            //     'payment_mode' => $paymentMode,
            //     'amount' => $amount,
            //     'billing_name' => $billingName,
            //     'billing_email' => $billingEmail,
            // ]);

            // Optionally, redirect to a success page or render success view
            return Inertia::render('Payment/PaymentResponse', [
                'response' => $responseParams,
                'status' => 'success',
                'message' => 'Payment was successful!'
            ]);

        } else {
            // Handle failure
            $failureMessage = $responseParams['failure_message'] ?? 'Unknown error occurred';

            // Optionally, redirect to a failure page or render failure view
            return Inertia::render('Payment/PaymentResponse', [
                'response' => $responseParams,
                'status' => 'failure',
                'message' => 'Payment failed: ' . $failureMessage
            ]);
        }
    }


    public function handleCancel(Request $request)
    {
        // Get the encrypted response from the payment gateway
        $encResponse = $request->input('encResp');

        // Decrypt the response using your CryptoService
        $decryptedResponse = $this->cryptoService->decrypt($encResponse);

        // Optionally, log the canceled transaction details for auditing
        Log::info('Payment Canceled:', [
            'response' => $decryptedResponse,
        ]);

        // Parse the decrypted response to handle it if necessary
        parse_str($decryptedResponse, $responseArray);

//        dd($responseArray);
        // Return the cancel view with relevant data, if needed
        return Inertia::render('Payment/PaymentCancel', [
            'merchantId' => $this->merchantId,
            'accessCode' => $this->accessCode,
            'message' => 'Payment was canceled!',
            'status' => 'canceled',
            'response' => $responseArray,
        ]);
    }

}
