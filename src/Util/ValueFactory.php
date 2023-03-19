<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Util;

use DevCircleDe\Attrenv\Attribute\EnvironmentValue;
use DevCircleDe\Attrenv\Decorator\EnvParserDecorator;
use DevCircleDe\Attrenv\ValueObject\MetaData;
use DevCircleDe\Attrenv\ValueObject\Type;
use DevCircleDe\Attrenv\ValueObject\Value;
use DevCircleDe\EnvReader\Exception\ConvertionException;
use DevCircleDe\EnvReader\Exception\NotFoundException;
use DI\Attribute\Inject;

/**
 * @psalm-api
 */
class ValueFactory
{
    /**
     * @param EnvParserDecorator $envParser
     */
    public function __construct(
        #[Inject]
        private readonly EnvParserDecorator $envParser
    ) {
    }

    /**
     * @param MetaData $metaData
     * @return Value|null
     */
    public function createValueFromMetaData(MetaData $metaData): ?Value
    {
        /** @var EnvironmentValue $attr */
        $attr = $metaData->getAttribute()->newInstance();
        $propertyName = $metaData->getName();
        if (null === ($envName = $attr->getEnvName())) {
            $envName = strtoupper(preg_replace('/([A-Z])/', '_$1', $propertyName));
        }
        if ($attrType = $attr->getType()) {
            $types = [new Type($attrType, $metaData->getType()->allowsNull())];
        } else {
            $types = $this->getVariableTypes($metaData->getType());
        }
        // Try to find the best matching type
        foreach ($types as $type) {
            try {
                return new Value(
                    $propertyName,
                    $this->getEnvParser()->parse($envName, $type->getType()),
                    $type->allowsNull()
                );
            } catch (ConvertionException $exception) {
                // do nothing here
            } catch (NotFoundException $exception) {
                if ($type->allowsNull()) {
                    return new Value(
                        $propertyName,
                        null,
                        $type->allowsNull()
                    );
                }
            }
        }

        return null;
    }

    /**
     * @param \ReflectionNamedType|\ReflectionUnionType $type
     * @return array|Type[]
     */
    private function getVariableTypes(\ReflectionNamedType|\ReflectionUnionType $type): array
    {
        $types = [];
        if ($type instanceof \ReflectionUnionType) {
            /** @psalm-var \ReflectionIntersectionType|\ReflectionNamedType $subType */
            foreach ($type->getTypes() as $subType) {
                if ($subType instanceof \ReflectionIntersectionType) {
                    continue;
                }
                $types = array_merge($types, $this->getVariableTypes($subType));
            }

            return $types;
        }

        if (!$type->isBuiltin()) {
            throw new \LogicException('Only buildInTypes are allowed');
        }

        return [new Type($type->getName(), $type->allowsNull())];
    }

    /**
     * @return EnvParserDecorator
     */
    public function getEnvParser(): EnvParserDecorator
    {
        return $this->envParser;
    }
}
