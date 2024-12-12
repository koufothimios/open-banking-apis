<?php

namespace OpenBanking;

class AlphaBankAPI {
    private $baseOauthUrl;
    private $baseResourceUrl;
    private $redirectUri;
    private $clientId;
    private $clientSecret;

    public function __construct($baseOauthUrl, $baseResourceUrl, $clientId, $clientSecret, $redirectUri) {
        $this->baseOauthUrl = rtrim($baseOauthUrl, '/');
        $this->baseResourceUrl = rtrim($baseResourceUrl, '/');
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
    }

    /**
     * Fetch an access token using cURL.
     *
     * @param string $grantType
     * @param string $scope
     * @return array
     */
    public function getAuthToken($grantType = 'client_credentials', $scope = 'account-info-setup'){
        $url = $this->baseOauthUrl . '/token';
        $credentials = base64_encode("{$this->clientId}:{$this->clientSecret}");

        $postData = [
            'grant_type' => $grantType,
            'scope' => $scope,
        ];

        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
            "Authorization: Basic $credentials",
        ];

        $response = $this->makeCurlRequest($url, $headers, http_build_query($postData));

        return json_decode($response, true);
    }

    function getAccessToken($authorizationCode){
        $url = $this->baseOauthUrl . '/token';
        $credentials = base64_encode("{$this->clientId}:{$this->clientSecret}");

        $postData = [
            'grant_type' => 'authorization_code',
            'code' => $authorizationCode,
            'redirect_uri' => $this->redirectUri,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];

        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
            "Authorization: Basic $credentials",
        ];

        $response = $this->makeCurlRequest($url, $headers, http_build_query($postData));

        return json_decode($response, true);
    }

    function makeAccountRequest($subscriptionKey, $authToken)
    {
        $url = $this->baseResourceUrl . '/accounts/v1/account-requests';

        // Headers for the request
        $headers = [
            "Content-Type: application/json",
            "Ocp-Apim-Subscription-Key: $subscriptionKey",
            "Authorization: Bearer $authToken"
        ];

        $postData = [
            "Risk" => null, // Empty object
            "ProductIdentifiers" => null
        ];

        $response = $this->makeCurlRequest($url, $headers, json_encode($postData));

        return json_decode($response, true);
    }

    function getAllBalances($subscriptionKey, $accessToken){
        $url = $this->baseResourceUrl . '/accounts/v1/balances';

        $headers = [
            "Authorization: Bearer $accessToken",
            "Ocp-Apim-Subscription-Key: $subscriptionKey",
        ];

        $response = $this->makeCurlRequest($url, $headers, null);

        return json_decode($response, true);
    }

    function generateLoginUrl($accountRequestId) {
        return $this->baseOauthUrl . "/authorize?client_id=$this->clientId&response_type=code&scope=account-info&redirect_uri=$this->redirectUri&request=$accountRequestId";
    }

    /**
     * Makes a cURL POST request.
     */
    private function makeCurlRequest($url, $headers, $data = null) {
        $postData = // URL-encode the data

        $ch = curl_init($url);

        if($data)curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if($data)curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // Set URL-encoded data

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $errorMessage = curl_error($ch);
            curl_close($ch);
            throw new \Exception("cURL Error: $errorMessage");
        }

        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception("HTTP Error $httpCode: $response");
        }

        return $response;
    }
}