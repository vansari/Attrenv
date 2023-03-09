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

class PropertyFactory
{

    public function create(MetaData $metaData, \ReflectionProperty $property, EnvParser $envParser): ?Value
    {
        /** @var EnvironmentValue $attr */
        $attr = $metaData->getAttribute()->newInstance();
        $propertyName = $metaData->getName();
        if (null === ($envName = $attr->getEnvName())) {
            $envName = strtoupper(preg_replace('/([A-Z])/', '_$1', $propertyName));
        }
        if ($envType = $attr->getType()) {
            $envTypes = [new Type($envType, $property->getType()->allowsNull())];
        } else {
            $envTypes = $this->getPropertyTypes($metaData->getType());
        }
        foreach ($envTypes as $envType) {
            try {
                return new Value(
                    $propertyName,
                    $envParser->parse($envName, $envType->getType()),
                    $envType->allowsNull()
                );
            } catch (ConvertionException $exception) {
                // do nothing here
            } catch (NotFoundException $exception) {
                if ($envType->allowsNull()) {
                    return new Value(
                        $propertyName,
                        null,
                        $envType->allowsNull()
                    );
                }
            }
        }

        return null;
    }


    private function getPropertyTypes(\ReflectionNamedType|\ReflectionUnionType $type): array
    {
        $types = [];
        if ($type instanceof \ReflectionUnionType) {
            foreach ($type->getTypes() as $unionType) {
                $types = array_merge($types, $this->getPropertyTypes($unionType));
            }

            return $types;
        }

        if (!$type->isBuiltin()) {
            throw new \LogicException('Only buildInTypes are allowed');
        }

        return [new Type($type->getName(), $type->allowsNull())];
    }
}