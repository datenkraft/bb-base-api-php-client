<?php

namespace Config;

use Datenkraft\Backbone\Client\BaseApi\ClientFactory;
use Datenkraft\Backbone\Client\BaseApi\Config;
use Jane\OpenApiRuntime\Client\Client;
use Datenkraft\Backbone\Client\BaseApi\Exceptions\ConfigException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

/**
 * Class ConfigTest
 * @package Config
 * @coversDefaultClass \Datenkraft\Backbone\Client\BaseApi\Config
 */
class ConfigTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @param array $expected
     * @throws ConfigException
     * @dataProvider createDataProvider
     */
    public function testCreate(array $expected)
    {
        $config = Config::create($expected);
        $this->assertInstanceOf(Config::class, $config);
        $this->assertSame($expected['clientId'], $config->getClientId());
    }

    public function createDataProvider(): array
    {
        return array(
            array(['clientId' => '1', 'clientSecret' => '2', 'oAuthScopes' => ['sku-usage:add'], 'oAuthTokenUrl' => 'https://bb_authorization_api:3000/oauth/token']),
        );
    }


}
