<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Parser;

/**
 * @psalm-api
 */
interface ParserInterface
{
    /**
     * @psalm-param class-string $class
     */
    public function parse(string $class): object;
}
