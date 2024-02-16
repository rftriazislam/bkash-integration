<?php

namespace App\Library\BkashLibrary;

abstract class AbstractBkash implements BkashInterface
{
    protected $apiUrl;
    protected $baseUrlApi;
    protected $username;
    protected $password;
    protected $app_key;
    protected $app_secret;

    protected function setUsername($username)
    {
        $this->username = $username;
    }

    protected function getUsername()
    {
        return $this->username;
    }

    protected function setPassword($password)
    {
        $this->password = $password;
    }

    protected function getPassword()
    {
        return $this->password;
    }
    protected function setAppKey($app_key)
    {
        $this->app_key = $app_key;
    }

    protected function getAppKey()
    {
        return $this->app_key;
    }
    protected function setAppSecret($app_secret)
    {
        $this->app_secret = $app_secret;
    }

    protected function getAppSecret()
    {
        return $this->app_secret;
    }

    protected function setApiUrl($url)
    {
        return $this->baseUrl($url);
    }
    private function baseUrl($url)
    {
        if (config("bkash.sandbox") == $url) {
            $this->apiUrl = 'https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized';
        } else {
            $this->apiUrl = 'https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized';
        }
    }

    protected function getApiUrl()
    {
        return $this->apiUrl;
    }
    /**
     * Request Headers
     * @param   string $url
     * @param   string $refresh_token
     * @param   string $account
     * @return  mixed
     * 
     */
    protected function getUrlToken($url, $refresh_token = null)
    {
        session()->forget('bkash_token');
        session()->forget('bkash_token_type');
        session()->forget('bkash_refresh_token');
        $post_token = array(
            'app_key'          => $this->app_key,
            'app_secret'       =>  $this->app_secret,
            'refresh_token'    => $refresh_token,
        );
        $url          = curl_init($this->apiUrl . $url);
        $post_token   = json_encode($post_token);
        $header       = array(
            'Content-Type:application/json',
            "password:$this->password",
            "username:$this->username"
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $post_token);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);

        $response = json_decode($resultdata, true);
        if (array_key_exists('msg', $response)) {
            return $response;
        }
        if (isset($response['id_token']) && isset($response['token_type']) && isset($response['refresh_token'])) {
            session()->put('bkash_token', $response['id_token']);
            session()->put('bkash_token_type', $response['token_type']);
            session()->put('bkash_refresh_token', $response['refresh_token']);
        }
        return $response;
    }

    /**
     * Request Headers
     * @param   string $url
     * @param   string $method
     * @param   string $data
     * @param   string $account
     * @return  mixed
     * 
     */
    protected function getUrl($url, $method, $data = null, $account = null)
    {
        $token       = session()->get('bkash_token');
        $app_key     = $this->app_key;

        $url         = curl_init($this->apiUrl . $url);
        $header      = array(
            'Content-Type:application/json',
            "authorization: $token",
            "x-app-key: $app_key"
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        if ($data) curl_setopt($url, CURLOPT_POSTFIELDS, $data);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);
        return json_decode($resultdata, true);
    }


    /**
     * Request Headers
     * @param   string $url
     * @param   string $paymentID
     * @return  mixed
     * 
     */
    protected function paymentExecute($paymentID, $url)
    {
        $post_token = array(
            'paymentID' => $paymentID
        );
        $url = curl_init($this->apiUrl . $url);
        $posttoken = json_encode($post_token);
        $app_key = $this->app_key;
        $header = array(
            'Content-Type:application/json',
            'Authorization:' . session()->get('bkash_token'),
            'X-APP-Key:' . $app_key
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $posttoken);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);

        return json_decode($resultdata, true);
    }

    /**
     * Request Headers
     * @param   string $url
     * @param   string $paymentID
     * @return  mixed
     * 
     */
    protected function paymentQuery($paymentID, $url)
    {
        $post_token     = array(
            'paymentID' => $paymentID
        );
        $url            = curl_init($this->apiUrl . $url);
        $posttoken      = json_encode($post_token);
        $app_key        = $this->app_key;
        $header         = array(
            'Content-Type:application/json',
            'Authorization:' . session()->get('bkash_token'),
            'X-APP-Key:' . $app_key
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $posttoken);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);

        return json_decode($resultdata, true);
    }
    /**
     * Request Headers
     * @param   string $url
     * @param   string $paymentID
     * @return  mixed
     * 
     */

    protected function transactionSearch($url, $data)
    {
        $url         = curl_init($this->apiUrl . $url);
        $app_key     = $this->app_key;
        $header      = array(
            'Content-Type:application/json',
            'Authorization:' . session()->get('bkash_token'),
            'x-app-key:' . $app_key
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        if ($data) curl_setopt($url, CURLOPT_POSTFIELDS, $data);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);

        return json_decode($resultdata, true);
    }
}
