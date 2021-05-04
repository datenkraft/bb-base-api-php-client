<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use ReflectionClass;
use ReflectionException;

abstract class TestCase extends BaseTestCase
{
    /**
     * @param callable $method
     * @param array $args
     * @return mixed
     * @throws ReflectionException
     * @noinspection PhpMissingParamTypeInspection
     */
    public function invokeMethod($method, array $args)
    {
        list($object, $methodName) = $method;
        $reflection = new ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $args);
    }

    /**
     * @return array
     */
    public function dataProviderTrueFalse(): array
    {
        return [
            [true],
            [false],
        ];
    }
}
