<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Decorator;

/**
 * Interface which you can implement for your own EnvParser
 */
interface EnvParserInterface
{
    public function parse(string $envName, string $type): mixed;
}
