<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Tests\data;

use DevCircleDe\Attrenv\Decorator\EnvParserInterface;

class CustomEnvParser implements EnvParserInterface
{
    public function parse(string $envName, string $type): mixed
    {
        return null;
    }
}
