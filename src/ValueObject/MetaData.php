<?php
declare(strict_types=1);

namespace DevCircleDe\Attrenv\ValueObject;

final class MetaData
{
    public function __construct(
        private readonly string $name,
        private readonly \ReflectionNamedType|\ReflectionUnionType $type,
        private \ReflectionAttribute $attribute,
    ) {}

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \ReflectionNamedType|\ReflectionUnionType
     */
    public function getType(): \ReflectionNamedType|\ReflectionUnionType
    {
        return $this->type;
    }

    /**
     * @return \ReflectionAttribute
     */
    public function getAttribute(): \ReflectionAttribute
    {
        return $this->attribute;
    }
}