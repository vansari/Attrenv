<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Parser\Constructor;

use DevCircleDe\Attrenv\Parser\ExplicitParserInterface;
use DevCircleDe\Attrenv\Util\MetaDataFactory;
use DevCircleDe\Attrenv\Util\ValueFactory;
use DevCircleDe\Attrenv\ValueObject\MetaData;
use DevCircleDe\Attrenv\ValueObject\ParameterValue;
use DevCircleDe\Attrenv\ValueObject\ParameterValueBag;
use DevCircleDe\Attrenv\ValueObject\Value;

/**
 * @psalm-api
 */
class ConstructorArgsParser implements ExplicitParserInterface
{
    public function __construct(
        private readonly MetaDataFactory $metaDataFactory,
        private readonly ValueFactory $valueFactory,
    ) {
    }

    /**
     * @psalm-param class-string $class
     */
    public function parse(string $class): object
    {
        $reflClass = new \ReflectionClass($class);
        $parameters = $reflClass->getConstructor()?->getParameters();

        if (null === $parameters) {
            return $reflClass->newInstance();
        }

        $parameterBag = $this->createParameterBag($parameters);

        $metaDataCollection = array_map(
            fn (\ReflectionParameter $reflParam): ?MetaData
                => $this->getMetaDataFactory()->createMetaDataFromReflection($reflParam),
            $parameters
        );

        $parsedValues = array_map(
            function (?MetaData $metaData) use ($reflClass): ?Value {
                if (null === $metaData) {
                    return null;
                }
                $reflProperty = $reflClass->getProperty($metaData->getName());
                return $this->getValueFactory()->createValueFromMetaData($metaData, $reflProperty);
            },
            $metaDataCollection
        );

        $this->setParameterValues($parsedValues, $parameterBag);
        if (count($parameters) !== count(($fetchValues = $parameterBag->fetchValues()))) {
            throw new \LogicException('Fetched parameter values did not match the count of constructor args.');
        }

        return $reflClass->newInstance(...$fetchValues);
    }

    /**
     * @param array $parameters
     * @return ParameterValueBag
     */
    private function createParameterBag(array $parameters): ParameterValueBag
    {
        $parameterBag = new ParameterValueBag();
        foreach ($parameters as $index => $parameter) {
            if ($parameter->isDefaultValueAvailable()) {
                $defaultValue = $parameter->getDefaultValue();
            }
            $parameterBag->addParameterValueAtIndex(
                new ParameterValue(
                    $index,
                    $parameter->getName(),
                    $parameter->allowsNull(),
                    $parameter->isDefaultValueAvailable(),
                    $defaultValue ?? null
                ),
                $index
            );
        }

        return $parameterBag;
    }

    /**
     * @param array $parsedValues
     * @param ParameterValueBag $parameterBag
     * @return void
     */
    private function setParameterValues(array $parsedValues, ParameterValueBag $parameterBag): void
    {
        foreach ($parsedValues as $index => $value) {
            $parameterValue = $parameterBag->getParameterValueAtIndex($index);
            if (null === $value) {
                if (!$parameterValue->hasDefaultValue()) {
                    if ($parameterValue->isNullable()) {
                        $parameterValue->setValue(
                            new Value(
                                $parameterValue->getName(),
                                null,
                                $parameterValue->isNullable()
                            )
                        );
                        continue;
                    }
                    throw new \LogicException(
                        "No value was set via EnvironmentValue. Constructor Parameter at index $index has"
                         . "no default value and is not nullable."
                    );
                }
                continue;
            }
            $parameterValue->setValue($value);
        }
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
