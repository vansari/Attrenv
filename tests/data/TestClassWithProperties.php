<?php
declare(strict_types=1);

namespace DevCircleDe\Attrenv\Tests\data;

use DevCircleDe\Attrenv\Attribute\EnvironmentValue;

class TestClassWithProperties
{
    #[EnvironmentValue]
    private ?string $databaseName = null;

    #[EnvironmentValue]
    private ?string $databasePassword = null;

    #[EnvironmentValue]
    private ?int $databasePort = null;

    private array $options = [];

    #[EnvironmentValue('json', 'DB_OPTION_JSON')]
    private array $optionsFromJson = [];

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