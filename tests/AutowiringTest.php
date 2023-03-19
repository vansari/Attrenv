<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Tests;

use DevCircleDe\Attrenv\Attrenv;
use DevCircleDe\Attrenv\Decorator\EnvParserInterface;
use DevCircleDe\Attrenv\Parser\AttributeParser;
use DevCircleDe\Attrenv\Tests\data\CustomEnvParser;
use PHPUnit\Framework\TestCase;

class AutowiringTest extends TestCase
{
    public function testAutowiring(): void
    {
        $attrenv = new Attrenv();
        $parser = $attrenv->getParser();
        $this->assertInstanceOf(AttributeParser::class, $parser);
    }

    public function testAutowiringWithCustomConfig(): void
    {
        $attrenv = new Attrenv(__DIR__ . '/data/testConfig.php');
        $parser = $attrenv->getParser();
        $this->assertInstanceOf(AttributeParser::class, $parser);

        $reflClass = new \ReflectionClass($attrenv);
        $containerReflProp = $reflClass->getProperty('container');
        $container = $containerReflProp->getValue($attrenv);

        $this->assertInstanceOf(CustomEnvParser::class, $container->get(EnvParserInterface::class));
    }
}
