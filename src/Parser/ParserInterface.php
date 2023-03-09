<?php
declare(strict_types=1);

namespace DevCircleDe\Attrenv\Parser;

use DevCircleDe\EnvReader\EnvParser;

interface ParserInterface
{
    public function parse(string $class): object;

    /**
     * @return EnvParser
     */
    public function getEnvParser(): EnvParser;
}