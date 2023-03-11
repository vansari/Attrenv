<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Util;

use DevCircleDe\Attrenv\Attribute\EnvironmentValue;
use DevCircleDe\Attrenv\ValueObject\MetaData;
use DevCircleDe\Attrenv\ValueObject\Type;
use DevCircleDe\Attrenv\ValueObject\Value;
use DevCircleDe\EnvReader\EnvParser;
use DevCircleDe\EnvReader\Exception\ConvertionException;
use DevCircleDe\EnvReader\Exception\NotFoundException;

/**
 * @psalm-api
 */
class ValueFactory
{
    private EnvParser $envParser;

    public function __construct(?EnvParser $envParser = null)
    {
        $this->envParser = $envParser ?? EnvParser::getInstance();
    }

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


    private function getVariableTypes(\ReflectionNamedType|\ReflectionUnionType $type): array
    {
        $types = [];
        if ($type instanceof \ReflectionUnionType) {
            foreach ($type->getTypes() as $unionType) {
                if ($unionType instanceof \ReflectionIntersectionType) {
                    continue;
                }
                $types = array_merge($types, $this->getVariableTypes($unionType));
            }

            return $types;
        }

        if (!$type->isBuiltin()) {
            throw new \LogicException('Only buildInTypes are allowed');
        }

        return [new Type($type->getName(), $type->allowsNull())];
    }

    /**
     * @return EnvParser
     */
    public function getEnvParser(): EnvParser
    {
        return $this->envParser;
    }
}
