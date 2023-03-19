<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Parser;

use DevCircleDe\Attrenv\Util\MetaDataFactory;
use DevCircleDe\Attrenv\Util\ParameterBagFactory;
use DevCircleDe\Attrenv\Util\ValueFactory;
use DevCircleDe\Attrenv\ValueObject\MetaData;
use DevCircleDe\Attrenv\ValueObject\ParameterValueBag;
use DevCircleDe\Attrenv\ValueObject\ParsedPropertyCollection;
use DevCircleDe\Attrenv\ValueObject\Value;
use DI\Attribute\Inject;
use ReflectionClass;
use ReflectionParameter;
use ReflectionProperty;

/**
 * @psalm-api
 */
class AttributeParser implements ParserInterface
{
    private ?ParsedPropertyCollection $parsedPropertyCollection = null;

    /**
     * @param MetaDataFactory $metaDataFactory
     * @param ValueFactory $valueFactory
     */
    public function __construct(
        #[Inject]
        private readonly MetaDataFactory $metaDataFactory,
        #[Inject]
        private readonly ValueFactory $valueFactory
    ) {
    }

    /**
     * @param string $class
     * @return object
     * @throws \ReflectionException
     * @psalm-param class-string $class
     */
    public function parse(string $class): object
    {
        $reflClass = new ReflectionClass($class);
        $parameterBag = $this->parseConstructorArgs($reflClass);
        $newClass = $reflClass->newInstance(...$parameterBag->fetchValues());

        $parsedProperties = $this->parseClassProperties($reflClass);

        foreach ($parsedProperties as $value) {
            if (null === $value || (null === $value->getValue() && !$value->isNullable())) {
                continue;
            }
            $reflProperty = $reflClass->getProperty($value->getName());
            $reflProperty->setValue($newClass, $value->getValue());
        }

        return $newClass;
    }

    /**
     * @param ReflectionClass $reflClass
     * @return ParameterValueBag
     */
    private function parseConstructorArgs(ReflectionClass $reflClass): ParameterValueBag
    {
        $parameters = $reflClass->getConstructor()?->getParameters();

        if (null === $parameters) {
            return new ParameterValueBag();
        }

        $values = $this->parseParameters($parameters);

        return (new ParameterBagFactory())->create($parameters, $values);
    }

    /**
     * @param ReflectionClass $reflClass
     * @return array
     * @psalm-return  array<array-key, Value|null>
     */
    private function parseClassProperties(ReflectionClass $reflClass): array
    {
        return $this->parseProperties($reflClass->getProperties());
    }

    /**
     * @return ParsedPropertyCollection
     */
    private function getParsedPropertyCollection(): ParsedPropertyCollection
    {
        if (null === $this->parsedPropertyCollection) {
            $this->parsedPropertyCollection = new ParsedPropertyCollection();
        }
        return $this->parsedPropertyCollection;
    }

    /**
     * @param ReflectionProperty[] $reflProps
     * @return Value[]
     * @psalm-return array<array-key, Value|null>
     */
    private function parseProperties(array $reflProps): array
    {
        return array_map(
            fn (MetaData $metaData) => $this->getValueFactory()->createValueFromMetaData($metaData),
            array_filter(
                array_map(
                    function (\ReflectionProperty $reflProp): ?MetaData {
                        if ($this->getParsedPropertyCollection()->has($reflProp->getName())) {
                            return null;
                        }
                        $this->setParsed($reflProp->getName());

                        return $this->getMetaDataFactory()->createMetaDataFromReflection($reflProp);
                    },
                    $reflProps
                )
            )
        );
    }

    /**
     * @param ReflectionParameter[] $parameters
     * @return Value[]
     * @psalm-return array<array-key, Value|null>
     */
    private function parseParameters(array $parameters): array
    {
        return array_map(
            function (?MetaData $metaData): ?Value {
                if (null === $metaData) {
                    return null;
                }

                return $this->getValueFactory()->createValueFromMetaData($metaData);
            },
            array_map(
                function (\ReflectionParameter $reflParam): ?MetaData {
                    $metaData = $this->getMetaDataFactory()->createMetaDataFromReflection($reflParam);
                    if (null !== $metaData) {
                        $this->setParsed($metaData->getName());
                    }
                    return $metaData;
                },
                $parameters
            )
        );
    }

    /**
     * @param string $name
     * @return void
     */
    private function setParsed(string $name): void
    {
        $this->getParsedPropertyCollection()->add($name);
    }

    /**
     * @return MetaDataFactory
     */
    private function getMetaDataFactory(): MetaDataFactory
    {
        return $this->metaDataFactory;
    }

    /**
     * @return ValueFactory
     */
    private function getValueFactory(): ValueFactory
    {
        return $this->valueFactory;
    }
}
