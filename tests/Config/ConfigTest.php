<?php

namespace Tests\Config;

use Datenkraft\Backbone\Client\BaseApi\Config;
use Datenkraft\Backbone\Client\BaseApi\Exceptions\ConfigException;
use Tests\TestCase;

/**
 * Class ConfigTest
 * @package Tests\Config
 * @coversDefaultClass \Datenkraft\Backbone\Client\BaseApi\Config
 */
class ConfigTest extends TestCase
{

    /**
     * @param bool $valid
     * @param array $configOptions
     * @throws ConfigException
     * @dataProvider createDataProvider
     * @covers ::create
     * @covers ::verifyConfigOptions
     */
    public function testCreate(bool $valid, array $configOptions): void
    {
        if (!$valid) {
            $this->expectException(ConfigException::class);
        }
        $config = Config::create($configOptions);
        $this->assertInstanceOf(Config::class, $config);
        $this->assertSame($configOptions['clientId'], $config->getClientId());
    }

    /**
     * @return \array[][]
     */
    public function createDataProvider(): array
    {
        return [
            [
                true,
                [
                    'clientId' => 'clientId',
                    'clientSecret' => 'clientSecret',
                    'oAuthScopes' => ['oAuthScopes'],
                ]
            ],
            [
                false,
                [
                    'clientSecret' => 'clientSecret',
                    'oAuthScopes' => ['oAuthScopes'],
                ]
            ],
            [
                false,
                [
                    'clientId' => 'clientId',
                    'oAuthScopes' => ['oAuthScopes'],
                ]
            ],
            [
                false,
                [
                    'clientId' => 'clientId',
                    'clientSecret' => 'clientSecret',
                ]
            ],
            [
                false,
                [
                    // empty
                ]
            ]
        ];
    }
}
