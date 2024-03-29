<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Tests\data;

use DevCircleDe\Attrenv\Attribute\EnvironmentValue;

class TestClassWithAttributeInConstructorAndDefaultValues
{
    public function __construct(
        #[EnvironmentValue(defaultValue: 'fooBar')]
        private readonly string $databaseName,
        #[EnvironmentValue]
        private readonly string $databasePassword,
        #[EnvironmentValue(defaultValue: 1234)]
        private readonly int $databasePort,
        private readonly array $options = [],
        #[EnvironmentValue(envName: 'DB_OPTION_JSON', type: 'json')]
        private readonly array $optionsFromJson = [],
    ) {
    }
    /**
     * @return string|null
     */
    public function getDatabaseName(): ?string
    {
        return $this->databaseName;
    }

    /**
     * @return string|null
     */
    public function getDatabasePassword(): ?string
    {
        return $this->databasePassword;
    }

    /**
     * @return int|null
     */
    public function getDatabasePort(): ?int
    {
        return $this->databasePort;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getOptionsFromJson(): array
    {
        return $this->optionsFromJson;
    }
}
