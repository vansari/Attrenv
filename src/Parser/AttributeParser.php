<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Parser;

use DevCircleDe\Attrenv\Attribute\AttributeEnvParser;
use ReflectionClass;

/**
 * @psalm-api
 */
class AttributeParser implements ParserInterface
{
    /**
     * @psalm-param class-string $class
     */
    public function parse(string $class): object
    {
        $reflClass = new ReflectionClass($class);
        $attr = $reflClass->getAttributes(AttributeEnvParser::class)[0] ?? null;
        if (null === $attr) {
            throw new \LogicException('Attribute "AttributeEnvParser" was not found at class ' . $class);
        }
        $attrEnvParser = $attr->newInstance();

        return $attrEnvParser->getParser()->parse($class);
    }
}
