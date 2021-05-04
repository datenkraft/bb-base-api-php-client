<?php

declare(strict_types=1);

namespace Datenkraft\Backbone\Client\BaseApi;

use Datenkraft\Backbone\Client\BaseApi\Exceptions\ConfigException;

class Config
{
    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var string
     */
    protected $oAuthTokenUrl;

    /**
     * @var array
     */
    protected $oAuthScopes;

    /**
     * @var string
     */
    protected $verifySsl;

    /**
     * @return string
     */
    public function getClientId(): string
    {
        return $this->clientId;
    }

    /**
     * @param string $clientId
     * @return Config
     */
    public function setClientId(string $clientId): Config
    {
        $this->clientId = $clientId;
        return $this;
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
     * @return Config
     */
    public function setClientSecret(string $clientSecret): Config
    {
        $this->clientSecret = $clientSecret;
        return $this;
    }

    /**
     * @return string
     */
    public function getOAuthTokenUrl(): string
    {
        return $this->oAuthTokenUrl;
    }

    /**
     * @param string $oAuthTokenUrl
     * @return Config
     */
    public function setOAuthTokenUrl(string $oAuthTokenUrl): Config
    {
        $this->oAuthTokenUrl = $oAuthTokenUrl;
        return $this;
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
     * @return Config
     */
    public function setOAuthScopes(array $oAuthScopes): Config
    {
        $this->oAuthScopes = $oAuthScopes;
        return $this;
    }

    /**
     * @return string
     */
    public function getVerifySsl(): string
    {
        return $this->verifySsl;
    }

    /**
     * @param string $verifySsl
     * @return Config
     */
    public function setVerifySsl(string $verifySsl): Config
    {
        $this->verifySsl = $verifySsl;
        return $this;
    }

    /**
     * @param array $config
     * @return Config
     * @throws ConfigException
     */
    public static function create(array $config): Config
    {
        $config = array_merge(require(__DIR__ . '/../config/config.php'), $config);
        static::verifyConfig($config);

        $configObject = new static();

        $configObject->setClientId($config['clientId']);
        $configObject->setClientSecret($config['clientSecret']);
        $configObject->setOAuthScopes($config['oAuthScopes']);
        $configObject->setOAuthTokenUrl($config['oAuthTokenUrl']);
        $configObject->setVerifySsl($config['verifySsl']);

        return $configObject;
    }

    /**
     * Config constructor.
     */
    protected function __construct()
    {
    }

    /**
     * @param array $config
     * @throws ConfigException
     */
    protected static function verifyConfig(array $config): void
    {
        if (empty($config['clientId'])
            || empty($config['clientSecret'])
            || !is_array($config['oAuthScopes'])
        ) {
            throw new ConfigException('Missing config key');
        }
    }
}
