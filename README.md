# OpenBanking AlphaBank API PHP Library

This library provides an interface to interact with the Alpha Bank Open Banking APIs. It simplifies the process of authenticating, making account requests, and retrieving account balances.

---

## Installation

1. Clone this repository or download the source code.
2. Include the library in your PHP project:
   ```php
   include '/path/to/OpenBanking/AlphaBankAPIs.php';

## Usage

1. Initialize the library
    ```php
    $alphaBank = new OpenBanking\AlphaBankAPI(
        'https://gw.api.alphabank.eu/sandbox/auth', // OAuth URL
        'https://gw.api.alphabank.eu/api/sandbox/', // Resource URL
        'your-client-id', // Client ID
        'your-client-secret', // Client Secret
        'https://your-redirect-uri.com' // Redirect URI
    );
    ```
2. Get Authentication Token
    ```php
    $authTokenResponse = $alphaBank->getAuthToken();
    echo 'Access Token: ' . $authTokenResponse['access_token'];
    ```
3. Make an Account Request and get login conset url
    ```php
    $accountRequestResponse = $alphaBank->makeAccountRequest(
        '5faa971974c740a692e3f36a0e011d2a', // Subscription Key
        $authTokenResponse['access_token'] // Access Token
    );
    
    $loginUrl = $alphaBank->generateLoginUrl($accountRequestResponse['AccountRequestId']);
    echo 'Login URL: ' . $loginUrl;
    ```
4. Exchange Authorization Code for Access Token
    ```php
    $accessTokenResponse = $alphaBank->getAccessToken('authorization-code-here');
    echo 'Access Token: ' . $accessTokenResponse['access_token'];
    ```
5. Retrieve Account Balances
    ```php
    $accountsBalancesResponse = $alphaBank->getAllBalances(
        '5faa971974c740a692e3f36a0e011d2a', // Subscription Key
        $accessTokenResponse['access_token'] // Access Token
    );
    
    var_dump($accountsBalancesResponse);
    ```
