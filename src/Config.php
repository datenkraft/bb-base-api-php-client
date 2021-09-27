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
     * @var bool
     */
    protected $verifySsl;

    /**
     * @var array
     */
    protected $configKeys;

    /**
     * @var array
     */
    protected $requiredConfigKeys;

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
    protected function setClientId(string $clientId): Config
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
    protected function setClientSecret(string $clientSecret): Config
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
    protected function setOAuthTokenUrl(string $oAuthTokenUrl): Config
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
    protected function setOAuthScopes(array $oAuthScopes): Config
    {
        $this->oAuthScopes = $oAuthScopes;
        return $this;
    }

    /**
     * @return bool
     */
    public function getVerifySsl(): bool
    {
        return $this->verifySsl;
    }

    /**
     * @param bool $verifySsl
     * @return Config
     */
    protected function setVerifySsl(bool $verifySsl): Config
    {
        $this->verifySsl = $verifySsl;
        return $this;
    }

    /**
     * @return array
     */
    public function getRequiredConfigKeys(): array
    {
        return $this->requiredConfigKeys;
    }

    /**
     * @param array $requiredConfigKeys
     * @return Config
     */
    protected function setRequiredConfigKeys(array $requiredConfigKeys): Config
    {
        $this->requiredConfigKeys = $requiredConfigKeys;
        return $this;
    }

    /**
     * @return array
     */
    public function getConfigKeys(): array
    {
        return $this->configKeys;
    }

    /**
     * @param array $configKeys
     * @return Config
     */
    protected function setConfigKeys(array $configKeys): Config
    {
        $this->configKeys = $configKeys;
        return $this;
    }

    /**
     * @param array $configOptions
     * @return Config
     * @throws ConfigException
     */
    public static function create(array $configOptions): Config
    {
        $configOptions = array_merge(require(__DIR__ . '/../config/config.php'), $configOptions);
        return new static($configOptions);
    }

    /**
     * Config constructor.
     * @param array $configOptions
     * @throws ConfigException
     */
    public function __construct(array $configOptions)
    {
        $this->setConfigKeys(
            [
                'clientId' => 'string',
                'clientSecret' => 'string',
                'oAuthScopes' => 'array',
                'oAuthTokenUrl' => 'string',
                'verifySsl' => 'boolean'
            ]
        );
        $this->setRequiredConfigKeys(['clientId', 'clientSecret']);
        $this->verifyConfigOptions($configOptions);
        $this->initByConfigOptions($configOptions);

        return $this;
    }

    /**
     * @param array $config
     * @throws ConfigException
     */
    protected function verifyConfigOptions(array $config): void
    {
        $this->verifyRequiredConfigKeys($config);
        $this->verifyUnknownConfigKeys($config);
        $this->verifyConfigKeys($config);
    }

    /**
     * @param array $config
     * @throws ConfigException
     */
    protected function verifyRequiredConfigKeys(array $config): void
    {
        foreach ($this->getRequiredConfigKeys() as $requiredConfigKeysKey) {
            if (empty($config[$requiredConfigKeysKey])) {
                throw new ConfigException('Missing required config key ' . $requiredConfigKeysKey);
            }
        }
    }

    /**
     * @param array $config
     * @throws ConfigException
     */
    protected function verifyConfigKeys(array $config): void
    {
        foreach ($this->getConfigKeys() as $key => $type) {
            if (isset($config[$key]) && gettype($config[$key]) != $type) {
                throw new ConfigException('Config key ' . $key . ' must be of type ' . $type);
            }
        }
    }

    /**
     * @param array $config
     * @throws ConfigException
     */
    protected function verifyUnknownConfigKeys(array $config): void
    {
        $allowedKeys = array_keys($this->getConfigKeys());
        $keys = array_keys($config);
        foreach ($keys as $key) {
            if (!in_array($key, $allowedKeys)) {
                throw new ConfigException('Unknown config key ' . $key);
            }
        }
    }

    /**
     * @param array $configOptions
     */
    protected function initByConfigOptions(array $configOptions): void
    {
        $this->setClientId($configOptions['clientId']);
        $this->setClientSecret($configOptions['clientSecret']);
        $this->setOAuthScopes($configOptions['oAuthScopes']);
        $this->setOAuthTokenUrl($configOptions['oAuthTokenUrl']);
        $this->setVerifySsl($configOptions['verifySsl']);
    }
}
