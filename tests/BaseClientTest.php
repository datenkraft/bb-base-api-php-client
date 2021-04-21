<?php

namespace BBBaseApiPhpClient;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use PHPUnit\Framework\TestCase;

class BaseClientTest extends TestCase
{
    protected BaseClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = new BaseClient();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    // Test OAuth2 authorization with thephpleague/oauth2-client
    // This test only serves as a demo for retrieving the token from a local instance of the auth server
    // It has to be adapted for using it in production/staging
    public function testAuthorize(): void
    {
        // Set the url of the auth server (local instance running in the same docker network)
        $this->client->setOAuthServerUrl('https://bb_authorization_api:3000');

        // Set a valid clientId, clientSecret and the requested scopes
        $this->client->setClientId('933daa6d-90da-44d5-8f2b-13f97fb2659c');
        $this->client->setClientSecret('8q6wAYC6mhomMA1SP2xjpykKSqGrGugfuitKerlI');
        $this->client->setOAuthScopes([]);

        // Disable SSL certificate validation (local auth server uses self-signed certificates)
        $guzzleClient = new Client(
            [
                RequestOptions::VERIFY => false,
            ]
        );

        // Authorize the client
        $this->client->authorize($guzzleClient);
        $token = $this->client->getAccessToken();

        // Decode header and payload of the JWT token and make assertions
        $header = json_decode(base64_decode(explode('.', $token)[0]), true);
        $payload = json_decode(base64_decode(explode('.', $token)[1]), true);

        $this->assertEquals('JWT', $header['typ']);
        $this->assertEquals($this->client->getClientId(), $payload['aud']);
        $this->assertEquals($this->client->getOAuthScopes(), $payload['scopes']);
    }
}
