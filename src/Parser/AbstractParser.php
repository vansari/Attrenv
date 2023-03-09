<?php
declare(strict_types=1);

namespace DevCircleDe\Attrenv\Parser;

use DevCircleDe\Attrenv\Util\MetaDataFactory;
use DevCircleDe\Attrenv\Util\PropertyFactory;
use DevCircleDe\EnvReader\EnvParser;

abstract class AbstractParser implements ParserInterface
{
    public function __construct(
        protected readonly EnvParser $envParser,
        protected readonly MetaDataFactory $metaDataFactory,
        protected readonly PropertyFactory $propertyFactory,
    ) {

    }

    /**
     * @return EnvParser
     */
    public function getEnvParser(): EnvParser
    {
        return $this->envParser;
    }
}