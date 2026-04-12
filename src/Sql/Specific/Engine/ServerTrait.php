<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Engine;

use Lagdo\DbAdmin\Driver\Sql\Specific\Connection\AbstractConnection;
use Lagdo\DbAdmin\Driver\Sql\Dto\UserDto;

trait ServerTrait
{
    /**
     * @return AbstractServer
     */
    abstract protected function _server(): AbstractServer;

    /**
     * Create a connection to a server
     *
     * @param array $options
     *
     * @return AbstractConnection|null
     */
    public function createConnection(array $options): AbstractConnection|null
    {
        return $this->_server()->createConnection($options);
    }

    /**
     * Connect to a database and a schema
     *
     * @param string $database  The database name
     * @param string $schema    The database schema
     *
     * @return AbstractConnection
     */
    public function openConnection(string $database, string $schema = ''): AbstractConnection
    {
        return $this->_server()->openConnection($database, $schema);
    }

    /**
     * Create a new connection to a database and a schema
     *
     * @param string $database  The database name
     * @param string $schema    The database schema
     *
     * @return AbstractConnection|null
     */
    public function newConnection(string $database, string $schema = ''): AbstractConnection|null
    {
        return $this->_server()->newConnection($database, $schema);
    }

    /**
     * @return AbstractConnection|null
     */
    public function connection(): AbstractConnection|null
    {
        return $this->_server()->connection();
    }

    /**
     * Close the connection to the server
     *
     * @return void
     */
    public function closeConnection(): void
    {
        $this->_server()->closeConnection();
    }

    /**
     * Get logged user
     *
     * @return string
     */
    public function user(): string
    {
        return $this->_server()->user();
    }

    /**
     * Get the users and hosts
     *
     * @param string $database  The database name
     *
     * @return array
     */
    public function getUsers(string $database): array
    {
        return $this->_server()->getUsers($database);
    }

    /**
     * Get the grants of a user on a given host
     *
     * @param string $user      The username
     * @param string $host      The host name
     *
     * @return UserDto
     */
    public function getUserGrants(string $user, string $host): UserDto
    {
        return $this->_server()->getUserGrants($user, $host);
    }

    /**
     * Get supported engines
     *
     * @return array
     */
    public function engines(): array
    {
        return $this->_server()->engines();
    }

    /**
     * Get sorted grouped list of collations
     *
     * @return array
     */
    public function collations(): array
    {
        return $this->_server()->collations();
    }

    /**
     * Get list of available routine languages
     *
     * @return array
     */
    public function routineLanguages(): array
    {
        return $this->_server()->routineLanguages();
    }

    /**
     * Get server variables
     *
     * @return array
     */
    public function variables(): array
    {
        return $this->_server()->variables();
    }

    /**
     * Get status variables
     *
     * @return array
     */
    public function statusVariables(): array
    {
        return $this->_server()->statusVariables();
    }

    /**
     * Get process list
     *
     * @return array
     */
    public function processes(): array
    {
        return $this->_server()->processes();
    }

    /**
     * Kill a process
     *
     * @param int
     *
     * @return bool
     */
    // public function killProcess($val): bool
    // {
    //     return $this->_server()->killProcess($val);
    // }

    /**
     * Get maximum number of connections
     *
     * @return int
     */
    // public function maxConnections(): int
    // {
    //     return $this->_server()->maxConnections();
    // }
}
