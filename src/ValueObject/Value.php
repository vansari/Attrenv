<?php
declare(strict_types=1);

namespace DevCircleDe\Attrenv\ValueObject;

final class Value
{

    public function __construct(
        private readonly string $name,
        private readonly mixed $value,
        private readonly bool $nullable,
    ) {}

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }
}