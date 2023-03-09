<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Parser;

use DevCircleDe\Attrenv\Util\MetaDataFactory;
use DevCircleDe\Attrenv\Util\ValueFactory;

interface ExplicitParserInterface extends ParserInterface
{
    public function getMetaDataFactory(): MetaDataFactory;

    public function getValueFactory(): ValueFactory;
}
