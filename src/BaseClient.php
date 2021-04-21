<?php

namespace BBBaseApiPhpClient;

use Exception;
use GuzzleHttp\Client;
use League\OAuth2\Client\Provider\GenericProvider;

class BaseClient
{

    protected string $oAuthServerUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected array $oAuthScopes;
    protected string $accessToken;

    /**
     * BaseClient constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     */
    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    /**
     * @param string $clientSecret
     */
    public function setClientSecret(string $clientSecret): void
    {
        $this->clientSecret = $clientSecret;
    }

    /**
     * @return array
     */
    public function getOAuthScopes(): array
    {
        return $this->oAuthScopes;
    }

    /**
     * @param array $oAuthScopes
     */
    public function setOAuthScopes(array $oAuthScopes): void
    {
        $this->oAuthScopes = $oAuthScopes;
    }

    /**
     * @return string
     */
    public function getOAuthServerUrl(): string
    {
        return $this->oAuthServerUrl;
    }

    /**
     * @param string $oAuthServerUrl
     */
    public function setOAuthServerUrl(string $oAuthServerUrl): void
    {
        $this->oAuthServerUrl = $oAuthServerUrl;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function authorize(Client $httpClient = null): bool
    {
        // Generic OAuth2 Provider
        $provider = new GenericProvider(
            [
                'clientId' => $this->clientId,
                'clientSecret' => $this->clientSecret,
                'scopes' => $this->oAuthScopes,
                'urlAuthorize' => $this->oAuthServerUrl . '/oauth/authorize',
                'urlAccessToken' => $this->oAuthServerUrl . '/oauth/token',
                'urlResourceOwnerDetails' => $this->oAuthServerUrl . '/oauth/resource',
            ]
        );

        if ($httpClient !== null) {
            $provider->setHttpClient($httpClient);
        }

        try {
            // Try to get an access token using the client credentials grant
            $this->accessToken = $provider->getAccessToken('client_credentials');
            return true;
        } catch (Exception $e) {
            // Failed to get the access token
            // Handle this error properly when error handling is implemented
            exit('Failed to authorize: ' . $e->getMessage());
        }
    }
}
