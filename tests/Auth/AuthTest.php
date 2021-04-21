<?php

namespace BBBaseApiPhpClient\Auth;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\TestCase;

class AuthTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    // Test OAuth2 authorization with thephpleague/oauth2-client
    // This only serves as a demo for retrieving the token from a local instance of the auth server
    // It has to be adapted for using it in production/staging
    public function testAuthorize(): void
    {
        // Url of the auth server (local instance running in the same docker network)
        $oAuthTokenUrl = 'https://bb_authorization_api:3000/oauth/token';

        // Valid clientId, clientSecret and requested scopes
        $clientId = '933daa6d-90da-44d5-8f2b-13f97fb2659c';
        $clientSecret = '8q6wAYC6mhomMA1SP2xjpykKSqGrGugfuitKerlI';
        $oAuthScopes = [];

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

        $this->assertEquals('JWT', $header['typ']);
        $this->assertEquals($clientId, $payload['aud']);
        $this->assertEquals($oAuthScopes, $payload['scopes']);
    }
}
