<?php

declare(strict_types=1);

namespace Datenkraft\Backbone\Client\BaseApi;

use Datenkraft\Backbone\Client\BaseApi\Auth\Auth;
use Datenkraft\Backbone\Client\BaseApi\Config;
use Datenkraft\Backbone\Client\BaseApi\Exceptions\ConfigException;
use GuzzleHttp\RequestOptions;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Client\Common\Plugin\HeaderAppendPlugin;
use Http\Client\Common\PluginClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Jane\OpenApiRuntime\Client\Client;

class ClientFactory
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $token;

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        if (is_null($this->token)){
            $this->setToken($this->generateToken());
        }
        return $this->token;
    }

    /**
     * @param string $token
     * @return self
     */
    protected function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    /**
     * ClientFactory constructor.
     * @param array $config
     * @throws ConfigException
     */
    public function __construct(array $config = [])
    {
        $this->config = Config::create($config);
    }

    /**
     * @param string $clientClass
     * @param string|null $endpointUrl
     * @return Client
     * @psalm-template ConcreteClientType of object
     * @psalm-param class-string<ConcreteClientType> $clientClass
     * @psalm-return ConcreteClientType
     */
    public function createClient(string $clientClass, string $endpointUrl = null): Client
    {
        /**
         * @var Client $clientClass
         */

        $plugins = [];
        $plugins[] = new HeaderAppendPlugin(
            ['Authorization' => "Bearer " . $this->getToken()]
        );

        if (null !== $endpointUrl){
            $guzzleOptions = [];
            //check if local call to disable ssl verification
            //if (strpos($endpointUrl, "://localhost") !== false){
                $guzzleOptions[RequestOptions::VERIFY] = false;
            //}

            $httpClient = $this->createHttpClient($guzzleOptions);

            $uri = Psr17FactoryDiscovery::findUriFactory()->createUri($endpointUrl);
            $plugins[] = new AddHostPlugin($uri);
            $httpClient = new PluginClient($httpClient, $plugins);

            /** @noinspection PhpUndefinedMethodInspection */
            $client = $clientClass::create($httpClient);
        } else {
            /** @noinspection PhpUndefinedMethodInspection */
            $client = $clientClass::create(null, $plugins);
        }

        return $client;
    }

    protected function createHttpClient(array $guzzleOptions = []): \GuzzleHttp\Client
    {
        return new \GuzzleHttp\Client($guzzleOptions);
    }

    /**
     * @return string
     */
    protected function generateToken(): string
    {
        return Auth::authorize(
            $this->getConfig()->getClientId(),
            $this->getConfig()->getClientSecret(),
            $this->getConfig()->getOAuthScopes(),
            $this->createHttpClient([RequestOptions::VERIFY => false]),
            $this->getConfig()->getOAuthTokenUrl()
        );
    }
}
