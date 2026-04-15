<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Engine;

use Lagdo\DbAdmin\Driver\Sql\Connection\AbstractConnection;
use Lagdo\DbAdmin\Driver\Sql\Dto\UserDto;
use Closure;

interface ServerInterface
{
    /**
     * Create a connection to a server
     *
     * @param array $options
     *
     * @return AbstractConnection|null
     */
    public function createConnection(array $options): AbstractConnection|null;

    /**
     * Connect to a database and a schema
     *
     * @param string $database  The database name
     * @param string $schema    The database schema
     *
     * @return AbstractConnection
     */
    public function openMainConnection(string $database, string $schema = ''): AbstractConnection;

    /**
     * Create a new connection to a database and a schema
     *
     * @param string $database  The database name
     * @param string $schema    The database schema
     *
     * @return AbstractConnection|null
     */
    public function openNewConnection(string $database, string $schema = ''): AbstractConnection|null;

    /**
     * @return AbstractConnection|null
     */
    public function connection(): AbstractConnection|null;

    /**
     * Execute the given closure using the provided connection.
     *
     * @param AbstractConnection $connection
     * @param Closure $function
     *
     * @return void
     */
    public function withConnection(AbstractConnection $connection, Closure $function): void;

    /**
     * Close the connection to the server
     *
     * @return void
     */
    public function closeConnection(): void;

    /**
     * Get logged user
     *
     * @return string
     */
    public function user(): string;

    /**
     * Get the users and hosts
     *
     * @param string $database  The database name
     *
     * @return array
     */
    public function getUsers(string $database): array;

    /**
     * Get the grants of a user on a given host
     *
     * @param string $user      The username
     * @param string $host      The host name
     *
     * @return UserDto
     */
    public function getUserGrants(string $user, string $host): UserDto;

    /**
     * Get supported engines
     *
     * @return array
     */
    public function engines(): array;

    /**
     * Get sorted grouped list of collations
     *
     * @return array
     */
    public function collations(): array;

    /**
     * Get list of available routine languages
     *
     * @return array
     */
    public function routineLanguages(): array;

    /**
     * Get server variables
     *
     * @return array
     */
    public function variables(): array;

    /**
     * Get status variables
     *
     * @return array
     */
    public function statusVariables(): array;

    /**
     * Get process list
     *
     * @return array
     */
    public function processes(): array;

    /**
     * Kill a process
     *
     * @param int
     *
     * @return bool
     */
    // public function killProcess($val): bool;

    /**
     * Get maximum number of connections
     *
     * @return int
     */
    // public function maxConnections(): int;
}
