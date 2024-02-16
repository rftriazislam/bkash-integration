<?php

namespace App\Library\BkashLibrary;

interface BkashInterface
{
    public function cPayment($data);
    public function executePayment($paymentID);
    public function queryPayment($paymentID);
    // public function refreshToken($refresh_token);
    public function searchTransaction($trxID);
}
