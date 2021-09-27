<?php

declare(strict_types=1);

namespace Tests\Config;

use Datenkraft\Backbone\Client\BaseApi\Config;
use Datenkraft\Backbone\Client\BaseApi\Exceptions\ConfigException;
use Generator;
use ReflectionException;
use Tests\TestCase;

/**
 * Class ConfigTest
 * @package Tests\Config
 * @coversDefaultClass \Datenkraft\Backbone\Client\BaseApi\Config
 */
class ConfigTest extends TestCase
{
    /**
     * @var Config
     */
    protected $object;

    /**
     * @var array
     */
    protected $configOptions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = $this->getMockBuilder(Config::class)->onlyMethods([])->disableOriginalConstructor()->getMock();
        $this->configOptions = [
            'clientId' => 'clientId',
            'clientSecret' => 'clientSecret',
            'oAuthScopes' => ['oAuthScopes'],
            'oAuthTokenUrl' => 'oAuthTokenUrl',
            'verifySsl' => false
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @param bool $valid
     * @param array $configOptions
     * @throws ConfigException
     * @dataProvider dataProviderTestCreate
     * @covers ::create
     */
    public function testCreate(bool $valid, array $configOptions): void
    {
        if (!$valid) {
            $this->expectException(ConfigException::class);
        }
        $config = Config::create($configOptions);
        $this->assertInstanceOf(Config::class, $config);
    }

    /**
     * @return Generator
     */
    public function dataProviderTestCreate(): Generator
    {
        yield 'valid_all_keys_set' => [
            true,
            [
                'clientId' => 'clientId',
                'clientSecret' => 'clientSecret',
                'oAuthScopes' => ['oAuthScopes'],
                'oAuthTokenUrl' => 'oAuthTokenUrl',
                'verifySsl' => false,
            ]
        ];
        yield 'valid_only_required_keys_set' => [
            true,
            [
                'clientId' => 'clientId',
                'clientSecret' => 'clientSecret',
            ]
        ];
        yield 'invalid_required_key_not_set' => [
            false,
            [
                'clientId' => 'clientId',
            ]
        ];
        yield 'invalid_no_key_set' => [
            false,
            []
        ];
        yield 'invalid_wrong_key_type' => [
            false,
            [
                'clientId' => 1,
                'clientSecret' => 'clientSecret',
            ]
        ];
        yield 'invalid_unknown_key' => [
            false,
            [
                'clientId' => 'clientId',
                'clientSecret' => 'clientSecret',
                'unknownKey' => '',
            ]
        ];
    }

    /**
     * @covers ::__construct
     * @covers ::setConfigKeys
     * @covers ::setRequiredConfigKeys
     * @covers ::getConfigKeys
     * @covers ::getRequiredConfigKeys
     * @throws ReflectionException
     */
    public function testConstruct(): void
    {
        $this->object = $this
            ->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['verifyConfigOptions', 'initByConfigOptions'])
            ->getMock();

        $this->object->expects($this->once())->method('verifyConfigOptions')->with($this->configOptions);
        $this->object->expects($this->once())->method('initByConfigOptions')->with($this->configOptions);

        $result = $this->invokeMethod([$this->object, '__construct'], [$this->configOptions]);

        $this->assertInstanceOf(Config::class, $result);

        $this->assertIsArray($result->getConfigKeys());
        $this->assertIsArray($result->getRequiredConfigKeys());
    }

    /**
     * @covers ::verifyConfigOptions
     * @throws ReflectionException
     */
    public function testVerifyConfigOptions(): void
    {
        $this->object = $this
            ->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['verifyRequiredConfigKeys', 'verifyUnknownConfigKeys', 'verifyConfigKeys'])
            ->getMock();

        $this->object->expects($this->once())->method('verifyRequiredConfigKeys')->with($this->configOptions);
        $this->object->expects($this->once())->method('verifyUnknownConfigKeys')->with($this->configOptions);
        $this->object->expects($this->once())->method('verifyConfigKeys')->with($this->configOptions);

        $this->invokeMethod([$this->object, 'verifyConfigOptions'], [$this->configOptions]);
    }

    /**
     * @covers ::verifyRequiredConfigKeys
     * @dataProvider dataProviderTestVerifyRequiredConfigKeys
     * @throws ReflectionException
     */
    public function testVerifyRequiredConfigKeys(array $required, array $config, ?string $exception = null): void
    {
        $this->object = $this
            ->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getRequiredConfigKeys'])
            ->getMock();

        $this->object->expects($this->once())->method('getRequiredConfigKeys')->willReturn($required);

        if ($exception) {
            $this->expectException(ConfigException::class);
            $this->expectExceptionMessage($exception);
        }

        $this->invokeMethod([$this->object, 'verifyRequiredConfigKeys'], [$config]);
    }

    /**
     * @return Generator
     */
    public function dataProviderTestVerifyRequiredConfigKeys(): Generator
    {
        yield 'valid_1' => [['requiredKey'], ['requiredKey' => 'value', 'optionalKey' => 'value']];
        yield 'valid_2' => [['requiredKey1', 'requiredKey2'], ['requiredKey1' => 'value', 'requiredKey2' => 'value']];
        yield 'invalid_1' => [['requiredKey'], ['optionalKey' => 'value'], 'Missing required config key requiredKey'];
        yield 'invalid_2' => [
            ['requiredKey1', 'requiredKey2'],
            ['requiredKey1' => 'value', 'optionalKey' => 'value'],
            'Missing required config key requiredKey2'
        ];
    }

    /**
     * @covers ::verifyConfigKeys
     * @dataProvider dataProviderTestVerifyConfigKeys
     * @throws ReflectionException
     */
    public function testVerifyConfigKeys(array $configKeys, array $config, ?string $exception = null): void
    {
        $this->object = $this
            ->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getConfigKeys'])
            ->getMock();

        $this->object->expects($this->once())->method('getConfigKeys')->willReturn($configKeys);

        if ($exception) {
            $this->expectException(ConfigException::class);
            $this->expectExceptionMessage($exception);
        }

        $this->invokeMethod([$this->object, 'verifyConfigKeys'], [$config]);
    }

    /**
     * @return Generator
     */
    public function dataProviderTestVerifyConfigKeys(): Generator
    {
        yield 'valid' => [['test' => 'string'], ['test' => 'test']];
        yield 'invalid' => [['test' => 'integer'], ['test' => 'test'], 'Config key test must be of type integer'];
    }

    /**
     * @covers ::verifyUnknownConfigKeys
     * @dataProvider dataProviderTestVerifyUnknownConfigKeys
     * @throws ReflectionException
     */
    public function testVerifyUnknownConfigKeys(array $configKeys, array $config, ?string $exception = null): void
    {
        $this->object = $this
            ->getMockBuilder(Config::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getConfigKeys'])
            ->getMock();

        $this->object->expects($this->once())->method('getConfigKeys')->willReturn($configKeys);

        if ($exception) {
            $this->expectException(ConfigException::class);
            $this->expectExceptionMessage($exception);
        }

        $this->invokeMethod([$this->object, 'verifyUnknownConfigKeys'], [$config]);
    }

    /**
     * @return Generator
     */
    public function dataProviderTestVerifyUnknownConfigKeys(): Generator
    {
        yield 'valid' => [['test' => 'string'], ['test' => 'test']];
        yield 'invalid' => [['test' => 'string'], ['unknown' => 'test'], 'Unknown config key unknown'];
    }

    /**
     * @covers ::initByConfigOptions
     * @covers ::setClientId
     * @covers ::setClientSecret
     * @covers ::setOAuthScopes
     * @covers ::setOAuthTokenUrl
     * @covers ::setVerifySsl
     * @covers ::getClientId
     * @covers ::getClientSecret
     * @covers ::getOAuthScopes
     * @covers ::getOAuthTokenUrl
     * @covers ::getVerifySsl
     * @throws ReflectionException
     */
    public function testInitByConfigOptions(): void
    {
        $this->invokeMethod([$this->object, 'initByConfigOptions'], [$this->configOptions]);

        $this->assertSame($this->configOptions['clientId'], $this->object->getClientId());
        $this->assertSame($this->configOptions['clientSecret'], $this->object->getClientSecret());
        $this->assertSame($this->configOptions['oAuthScopes'], $this->object->getOAuthScopes());
        $this->assertSame($this->configOptions['oAuthTokenUrl'], $this->object->getOAuthTokenUrl());
        $this->assertSame($this->configOptions['verifySsl'], $this->object->getVerifySsl());
    }
}
