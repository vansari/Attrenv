<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Util;

use DevCircleDe\Attrenv\Attribute\EnvironmentValue;
use DevCircleDe\Attrenv\ValueObject\MetaData;

class MetaDataFactory
{
    /**
     * @param \ReflectionProperty|\ReflectionParameter $reflection
     * @return MetaData|null
     */
    public function createMetaDataFromReflection(\ReflectionProperty|\ReflectionParameter $reflection): ?MetaData
    {
        $attribute = $reflection->getAttributes(EnvironmentValue::class)[0] ?? null;
        if (null === $attribute) {
            return null;
        }
        $name = $reflection->getName();
        $type = $reflection->getType();
        if (in_array($type, [null, \ReflectionIntersectionType::class])) {
            return null;
        }
        return new MetaData($name, $type, $attribute);
    }
}
