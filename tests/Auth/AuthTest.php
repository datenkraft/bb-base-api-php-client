<?php

namespace Tests\Auth;

use Datenkraft\Backbone\Client\BaseApi\Auth\Auth;
use Datenkraft\Backbone\Client\BaseApi\Exceptions\AuthException;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Tests\TestCase;

/**
 * Class AuthTest
 * @package Tests\Auth
 * @coversDefaultClass \Datenkraft\Backbone\Client\BaseApi\Auth\Auth
 */
class AuthTest extends TestCase
{
    /**
     * Test OAuth2 authorization with thephpleague/oauth2-client
     * This only serves as a demo for retrieving the token from a local instance of the auth server
     * It has to be adapted for using it in production/staging
     *
     * @throws AuthException
     * @covers
     */
    public function testAuthorize(): void
    {
        // Url of the auth server
        $oAuthTokenUrl = getenv('X_DATENKRAFT_OAUTH_TOKEN_URL');

        // Valid clientId, clientSecret and requested scopes
        $clientId = getenv('X_DATENKRAFT_CLIENT_ID');
        $clientSecret = getenv('X_DATENKRAFT_CLIENT_SECRET');
        $oAuthScopes = getenv('X_DATENKRAFT_OAUTH_SCOPE') ? [getenv('X_DATENKRAFT_OAUTH_SCOPE')] : false;

        if (!$oAuthTokenUrl || !$clientId || !$clientSecret || (!is_array($oAuthScopes) || count($oAuthScopes) === 0)) {
            $this->markTestSkipped('no oauth token url, client id, client secrete or oauth scopes set in environment variables');
        }

        // Disable SSL certificate validation (local auth server uses self-signed certificates)
        $guzzleClient = new Client(
            [
                RequestOptions::VERIFY => false,
            ]
        );

        // Authorize the client
        $token = Auth::authorize($clientId, $clientSecret, $oAuthScopes, $guzzleClient, $oAuthTokenUrl);

        // Decode header and payload of the JWT token and make assertions
        $header = json_decode(base64_decode(explode('.', $token)[0]), true);
        $payload = json_decode(base64_decode(explode('.', $token)[1]), true);

        var_dump($token);

        $this->assertEquals('JWT', $header['typ']);
        $this->assertEquals($clientId, $payload['aud']);
        $this->assertEquals($oAuthScopes, $payload['scopes']);
    }
}
