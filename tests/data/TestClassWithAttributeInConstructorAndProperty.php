<?php

declare(strict_types=1);

namespace DevCircleDe\Attrenv\Tests\data;

use DevCircleDe\Attrenv\Attribute\EnvironmentValue;

class TestClassWithAttributeInConstructorAndProperty
{
    #[EnvironmentValue(envName: 'DATABASE_NAME')]
    private string $dbName;

    private string $password;

    #[EnvironmentValue]
    private int $databasePort = 0;

    private array $options;

    #[EnvironmentValue(envName: 'DB_OPTION_JSON', type: 'json')]
    private array $config = [];

    public function __construct(
        #[EnvironmentValue(envName: 'DATABASE_HOSTNAME')]
        private readonly string $dbHostname,
        #[\SensitiveParameter]
        #[EnvironmentValue]
        string $databasePassword,
        private readonly ?string $comment,
        array $options = [],
    ) {
        $this->options = $options;
        $this->password = $databasePassword;
    }

    /**
     * @return string
     */
    public function getDbHostname(): string
    {
        return $this->dbHostname;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return string
     */
    public function getDbName(): string
    {
        return $this->dbName;
    }

    /**
     * @return int
     */
    public function getDatabasePort(): int
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
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
