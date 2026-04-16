<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Engine;

use Lagdo\DbAdmin\Driver\Sql\AbstractDbProxy;
use Lagdo\DbAdmin\Driver\Sql\Config\DriverConfig;
use Lagdo\DbAdmin\Driver\Sql\Connection\AbstractConnection;
use Lagdo\DbAdmin\Driver\Exception\AuthException;
use Lagdo\DbAdmin\Driver\Sql\Dto\UserDto;
use Closure;

abstract class AbstractServer extends AbstractDbProxy implements ServerInterface
{
    /**
     * @var AbstractConnection|null
     */
    protected AbstractConnection|null $connection = null;

    /**
     * @var DriverConfig
     */
    protected DriverConfig $config;

    /**
     * @return void
     */
    abstract protected function configure(): void;

    /**
     * @return void
     */
    abstract protected function connected(): void;

    /**
     * @param DriverConfig $config
     */
    final public function setConfig(DriverConfig $config)
    {
        $this->config = $config;
        // Fill the config with driver specific values.
        $this->configure();
    }

    /**
     * @inheritDoc
     */
    final public function connection(): AbstractConnection|null
    {
        return $this->connection;
    }

    /**
     * @inheritDoc
     * @throws AuthException
     */
    final public function openMainConnection(string $database, string $schema = ''): AbstractConnection
    {
        $this->closeConnection();
        $this->connection = $this->createConnection($this->config->options);
        if (!$this->connection?->open($database, $schema)) {
            throw new AuthException($this->_engine()->error());
        }

        $this->config->setDatabase($database, $schema);
        $this->connected();

        return $this->connection;
    }

    /**
     * @inheritDoc
     */
    final public function closeConnection(): void
    {
        $this->connection?->close();
        $this->connection = null;
    }

    /**
     * @inheritDoc
     */
    final public function openNewConnection(string $database, string $schema = ''): AbstractConnection|null
    {
        $connection = $this->createConnection($this->config->options());
        return !$connection || !$connection->open($database, $schema) ? null : $connection;
    }

    /**
     * @inheritDoc
     */
    final public function withConnection(AbstractConnection $connection, Closure $function): void
    {
        // Save the main connection, and use the provied one.
        $mainConnection = $this->connection;
        $this->connection = $connection;

        // Run the provided function.
        try {
            $function();
        } finally {
            // Reset the main connection.
            $this->connection = $mainConnection;
        }
    }

    /**
     * @inheritDoc
     */
    public function getUsers(string $database): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getUserGrants(string $user, string $host): UserDto
    {
        return new UserDto($user, $host);
    }

    /**
     * @inheritDoc
     */
    public function engines(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function collations(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function variables(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function statusVariables(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function routineLanguages(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function processes(): array
    {
        return [];
    }
}
