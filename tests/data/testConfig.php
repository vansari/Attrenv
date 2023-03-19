<?php

declare(strict_types=1);

use DevCircleDe\Attrenv\Decorator\EnvParserInterface;
use DevCircleDe\Attrenv\Tests\data\CustomEnvParser;

return [
    EnvParserInterface::class => new CustomEnvParser(),
];
