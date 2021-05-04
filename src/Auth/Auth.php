<?php

namespace Datenkraft\Backbone\Client\BaseApi\Auth;

use Datenkraft\Backbone\Client\BaseApi\Exceptions\AuthException;
use Exception;
use GuzzleHttp\Client;
use League\OAuth2\Client\Provider\GenericProvider;

class Auth
{

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @param array $oAuthScopes
     * @param Client|null $httpClient
     * @param string|null $oAuthTokenUrl
     * @return string
     * @throws AuthException
     */
    public static function authorize(
        string $clientId,
        string $clientSecret,
        array $oAuthScopes,
        Client $httpClient = null,
        string $oAuthTokenUrl = null
    ): string {
        // Load oAuthTokenUrl from config if it is not specified
        if ($oAuthTokenUrl === null) {
            $config = require(__DIR__  . '/../../config/config.php');
            $oAuthTokenUrl = $config['oAuthTokenUrl'];
        }

        // Generic OAuth2 Provider (urlResourceOwnerDetails and urlAuthorize are required but unused)
        $provider = new GenericProvider(
            [
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
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
            return $provider->getAccessToken('client_credentials', ['scope' => $oAuthScopes]);
        } catch (Exception $e) {
            // Failed to get the access token
            // Handle the error properly when error handling is implemented
            throw new AuthException('Failed to authorize: ' . $e->getMessage());
        }
    }
}
