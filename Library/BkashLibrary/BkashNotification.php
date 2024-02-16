<?php

namespace App\Library\BkashLibrary;

use Illuminate\Support\Facades\URL;

class BkashNotification extends AbstractBkash
{
    protected $data = [];
    protected $config = [];
    /**
     * BkashNotification constructor.
     */
    public function __construct()
    {
        $this->config = config('bkash');
        $this->setUsername($this->config['bkash_username']);
        $this->setPassword($this->config['bkash_password']);
        $this->setAppKey($this->config['bkash_app_key']);
        $this->setAppSecret($this->config['bkash_app_secret']);
        $this->setApiUrl($this->config['sandbox']);
    }

    public  function cPayment($request_data_json)
    {

        $response = $this->getToken();
        if (isset($response['id_token']) && $response['id_token']) {
            return $this->getUrl('/checkout/create', 'POST', $request_data_json);
        }
        return $response;
    }
    /**
     * Request Headers
     * @return  object
     * @return  array
     * @param   string $account
     */
    protected  function getToken()
    {
        return $this->getUrlToken('/checkout/token/grant', null);
    }

    /**
     * Request Headers
     * @return  object
     * @return  array
     * @param   string $paymentID
     */
    public function executePayment($paymentID)
    {
        $token          = session()->get('bkash_token');

        if (!$token) $this->getToken();
        return $this->paymentExecute($paymentID, '/checkout/execute');
    }
    /**
     * Request Headers
     * @return  object
     * @return  array
     * @param   string $paymentID
     */
    public function queryPayment($paymentID)
    {
        $token = session()->get('bkash_token');
        if (!$token) $this->getToken();
        return $this->paymentQuery($paymentID, '/checkout/payment/status');
    }

    /**
     * Request Headers
     * @return  object
     * @return  array
     * @param   string $trxID
     */
    public function searchTransaction($trxID)
    {
        $post_token = array(
            'trxID' => $trxID
        );
        $posttoken = json_encode($post_token);
        $this->getToken();
        return $this->transactionSearch("/checkout/general/searchTransaction", $posttoken);
    }



    /**
     * bkash Request Headers
     *
     * @return array
     */
    protected function headers()
    {
        return [
            "Content-Type"     => "application/json",
            "X-KM-IP-V4"       => $this->getIp(),
            "X-KM-Api-Version" => "v-0.2.0",
            "X-KM-Client-Type" => "PC_WEB"
        ];
    }
    /**
     * @return string|null
     */
    public function getIp()
    {
        return request()->ip();
    }
}
