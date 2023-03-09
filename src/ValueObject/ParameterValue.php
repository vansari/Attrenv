<?php
declare(strict_types=1);

namespace DevCircleDe\Attrenv\ValueObject;

final class ParameterValue
{
    private ?Value $value = null;

    public function __construct(
        private readonly int $index,
        private readonly string $name,
        private readonly bool $nullable = false,
        private readonly bool $hasDefaultValue = false,
        private readonly mixed $defaultValue = null
    ) {

    }

    public function setValue(Value $value): void
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getIndex(): int
    {
        return $this->index;
    }

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
    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }

    /**
     * @return Value|null
     */
    public function getValue(): ?Value
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function hasDefaultValue(): bool
    {
        return $this->hasDefaultValue;
    }

    /**
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }
}