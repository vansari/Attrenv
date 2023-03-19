<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Decorator;

use DevCircleDe\EnvReader\EnvParserInterface as EnvReaderParserInterface;

class EnvParserDecorator implements EnvParserInterface
{
    public function __construct(private readonly EnvParserInterface|EnvReaderParserInterface $decorated)
    {
    }

    public function parse(string $envName, string $type): mixed
    {
        return $this->decorated->parse($envName, $type);
    }
}
