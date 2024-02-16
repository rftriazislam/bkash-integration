<?php

namespace App\Http\Controllers;

use App\Library\BkashLibrary\BkashNotification;
use Illuminate\Http\Request;



class BkashPaymentController extends Controller
{

    public function index()
    {
        return view('bkash-payment');
    }
    public function createPayment(Request $request)
    {
        $inv                               = uniqid();
        $request['intent']                 = 'sale';
        $request['mode']                   = '0011';
        $request['payerReference']         = $inv;
        $request['currency']               = 'BDT';
        $request['amount']                 = 10;
        $request['merchantInvoiceNumber']  = $inv;
        $request['callbackURL']            = config("bkash.callbackURL") . '/' . $inv;
        $request_data_json                 = json_encode($request->all());
        $bN                                = new BkashNotification();
        $response                          = $bN->cPayment($request_data_json);

        if (isset($response['bkashURL'])) {
            return redirect()->away($response['bkashURL']);
        } else {
            return  redirect()->route('failed')->with(['message' => $response['statusMessage']]);
        }
    }

    public function callBack(Request $request)
    {

        $bN                                = new BkashNotification();

        if ($request->status == 'success') {
            $response                     =   $bN->executePayment($request->paymentID);
            if (!$response) {
                $response                 =   $bN->queryPayment($request->paymentID);
            }
            if (isset($response['statusCode']) && $response['statusCode'] == "0000" && $response['transactionStatus'] == "Completed") {
                // Payment success 
                return  redirect()->route('success')->with(['message' => 'Thank you for your payment', $response['trxID']]);
            }
            return  redirect()->route('failed')->with(['message' => $response['statusMessage']]);
        } else if ($request->status == 'cancel') {
            return  redirect()->route('cancel')->with(['message' => 'Your payment is canceled']);
        } else {
            return  redirect()->route('failed')->with(['message' => 'Your transaction is failed']);
        }
    }

    public function searchTnx($trxID)
    {
        $bN            = new BkashNotification();
        return $bN->searchTransaction($trxID);
    }

    public function refreshToken($refresh_token)
    {
        return $this->getUrlToken("/checkout/token/refresh", $refresh_token);
    }


    public function successPayment()
    {
        return view('success');
    }
    public function cancelPayment()
    {
        return view('failed');
    }
    public function failedPayment()
    {
        return view('failed');
    }
}
