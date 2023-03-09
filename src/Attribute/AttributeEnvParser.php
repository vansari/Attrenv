<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Attribute;

use DevCircleDe\Attrenv\Parser\ParserInterface;

#[\Attribute(\Attribute::TARGET_CLASS)]
class AttributeEnvParser
{
    public function __construct(private readonly ParserInterface $parser)
    {
    }

    /**
     * @return ParserInterface
     */
    public function getParser(): ParserInterface
    {
        return $this->parser;
    }
}
