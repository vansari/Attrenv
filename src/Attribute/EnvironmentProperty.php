<?php
declare(strict_types=1);

namespace DevCircleDe\Attrenv\Attribute;

#[\Attribute(\Attribute::TARGET_PROPERTY|\Attribute::TARGET_PARAMETER)]
class EnvironmentProperty
{
    public function __construct(private readonly null|string $type = null, private readonly null|string $envName = null,)
    {

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
}