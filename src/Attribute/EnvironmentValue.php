<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_PARAMETER)]
/**
 * @psalm-api
 */
class EnvironmentValue
{
    public function __construct(
        private readonly null|string $envName = null,
        private readonly mixed $defaultValue = null,
        private readonly null|string $type = null,
    ) {
    }

    /**
     * @return string|null
     */
    public function getEnvName(): ?string
    {
        return $this->envName;
    }

    /**
     * @return string|null
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue(): mixed
    {
        return $this->defaultValue;
    }
}
