<?php

namespace BBBaseApiPhpClient\Auth;

use Exception;
use GuzzleHttp\Client;
use League\OAuth2\Client\Provider\GenericProvider;

class Auth
{

    public static function authorize(
        string $clientId,
        string $clientSecret,
        array $oAuthScopes,
        Client $httpClient = null,
        string $oAuthTokenUrl = null
    ): string {
        // Load oAuthTokenUrl from config if it is not specified
        if ($oAuthTokenUrl === null) {
            $config = include('config/config.php');
            $oAuthTokenUrl = $config['oAuthTokenUrl'];
        }

        // Generic OAuth2 Provider (urlResourceOwnerDetails and urlAuthorize are required but unused)
        $provider = new GenericProvider(
            [
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'scopes' => $oAuthScopes,
                'urlAccessToken' => $oAuthTokenUrl,
                'urlResourceOwnerDetails' => '',
                'urlAuthorize' => '',
            ]
        );

        // Set custom HttpClient if specified
        if ($httpClient !== null) {
            $provider->setHttpClient($httpClient);
        }

        try {
            // Try to get an access token using the client credentials grant
            return $provider->getAccessToken('client_credentials');
        } catch (Exception $e) {
            // Failed to get the access token
            // Handle the error properly when error handling is implemented
            exit('Failed to authorize: ' . $e->getMessage());
        }
    }
}
