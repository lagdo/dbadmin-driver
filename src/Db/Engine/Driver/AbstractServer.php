<?php

namespace Lagdo\DbAdmin\Support\Db\Engine\Driver;

use Lagdo\DbAdmin\Support\Db\AbstractDbProxy;
use Lagdo\DbAdmin\Support\Db\Admin\Config\DriverConfig;
use Lagdo\DbAdmin\Support\Db\Engine\Connection\AbstractConnection;
use Lagdo\DbAdmin\Support\Exception\AuthException;
use Lagdo\DbAdmin\Support\Dto\UserDto;

abstract class AbstractServer extends AbstractDbProxy implements ServerInterface
{
    /**
     * @var AbstractConnection|null
     */
    protected AbstractConnection|null $connection = null;

    /**
     * @var AbstractConnection|null
     */
    protected AbstractConnection|null $mainConnection = null;

    /**
     * @var DriverConfig
     */
    protected DriverConfig $config;

    /**
     * @return void
     */
    abstract protected function starting(): void;

    /**
     * @return void
     */
    abstract protected function connected(): void;

    /**
     * @param array $options
     */
    public function initConnection(DriverConfig $config, array $options)
    {
        $this->config = $config;
        // Fill the config with driver specific values.
        $this->starting();
        // Create and set the main connection.
        $this->connection = $this->createConnection($options);
    }

    /**
     * @inheritDoc
     */
    public function connection(): AbstractConnection|null
    {
        return $this->connection;
    }

    /**
     * @inheritDoc
     * @throws AuthException
     */
    public function openConnection(string $database, string $schema = ''): AbstractConnection
    {
        if (!$this->connection->open($database, $schema)) {
            throw new AuthException($this->_driver()->error());
        }

        $this->config->setDatabase($database, $schema);

        if ($this->mainConnection === null) {
            $this->mainConnection = $this->connection;
            $this->connected();
        }

        return $this->connection;
    }

    /**
     * @inheritDoc
     */
    public function closeConnection(): void
    {
        $this->connection->close();
        $this->connection = null;
    }

    /**
     * @inheritDoc
     */
    public function newConnection(string $database, string $schema = ''): AbstractConnection|null
    {
        $connection = $this->createConnection($this->config->options());
        return !$connection || !$connection->open($database, $schema) ? null : $connection;
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
        $entity = new UserDto($user, $host);
        return $entity;
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
