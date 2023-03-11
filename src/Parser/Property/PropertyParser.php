<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Parser\Property;

use DevCircleDe\Attrenv\Parser\ExplicitParserInterface;
use DevCircleDe\Attrenv\Util\MetaDataFactory;
use DevCircleDe\Attrenv\Util\ValueFactory;
use DevCircleDe\Attrenv\ValueObject\MetaData;
use ReflectionClass;

/**
 * @psalm-api
 */
class PropertyParser implements ExplicitParserInterface
{
    public function __construct(
        private readonly MetaDataFactory $metaDataFactory,
        private readonly ValueFactory $valueFactory,
    ) {
    }

    /**
     * @psalm-param class-string $class
     */
    public function parse(mixed $class): object
    {
        $reflClass = new ReflectionClass($class);
        if ($reflClass->getConstructor()?->getParameters()) {
            throw new \InvalidArgumentException('Could not use class with Constructor Args');
        }
        $class = $reflClass->newInstance();
        $reflProps = $reflClass->getProperties();

        $properties = array_filter(
            array_map(
                fn (\ReflectionProperty $reflProp) =>
                    $this->getMetaDataFactory()->createMetaDataFromReflection($reflProp),
                $reflProps
            )
        );

        $parsedProperties = array_map(
            fn (MetaData $metaData) => $this->getValueFactory()->createValueFromMetaData($metaData),
            $properties
        );

        foreach ($parsedProperties as $value) {
            if (null === $value || (null === $value->getValue() && !$value->isNullable())) {
                continue;
            }
            $reflProperty = $reflClass->getProperty($value->getName());
            $reflProperty->setValue($class, $value->getValue());
        }

        return $class;
    }

    public function getMetaDataFactory(): MetaDataFactory
    {
        return $this->metaDataFactory;
    }

    public function getValueFactory(): ValueFactory
    {
        return $this->valueFactory;
    }
}
