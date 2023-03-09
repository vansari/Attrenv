<?php
declare(strict_types=1);

namespace DevCircleDe\Attrenv\Parser\Property;

use DevCircleDe\Attrenv\Parser\AbstractParser;
use DevCircleDe\Attrenv\ValueObject\MetaData;
use ReflectionClass;

class PropertyParser extends AbstractParser
{
    public function parse(mixed $class): object
    {
        $reflClass = new ReflectionClass($class);
        if($reflClass->getConstructor()?->getParameters()) {
            throw new \InvalidArgumentException('Could not use class with Constructor Args');
        }
        $class = $reflClass->newInstance();
        $reflProps = $reflClass->getProperties();

        $properties = array_filter(
            array_map(
                fn (\ReflectionProperty $reflProp) => $this->metaDataFactory->create($reflProp),
                $reflProps
            )
        );

        $parsedProperties = array_map(
            function (MetaData $metaData) use ($reflClass) {
                $reflProperty = $reflClass->getProperty($metaData->getName());
                return $this->propertyFactory->create($metaData, $reflProperty, $this->getEnvParser());
            },
            $properties
        );

        foreach ($parsedProperties as $value) {
            if (null === $value->getValue() && !$value->isNullable()) {
                continue;
            }
            $reflProperty = $reflClass->getProperty($value->getName());
            $reflProperty->setValue($class, $value->getValue());
        }

        return $class;
    }
}