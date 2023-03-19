<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv;

use DevCircleDe\Attrenv\Configuration\AttrenvConfig;
use DevCircleDe\Attrenv\Parser\AttributeParser;
use DevCircleDe\Attrenv\Parser\ParserInterface;
use DI\Container;

/**
 * @psalm-api
 */
class Attrenv
{
    private Container $container;

    public function __construct(?string $configPath = null)
    {
        $this->container = AttrenvConfig::getContainer($configPath);
    }

    public function getParser(string $parserName = AttributeParser::class): ParserInterface
    {
        return $this->container->get($parserName);
    }
}
