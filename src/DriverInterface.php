<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Db\AbstractConnection;
use Lagdo\DbAdmin\Driver\Driver\ConfigInterface;
use Lagdo\DbAdmin\Driver\Driver\ConnectionInterface;
use Lagdo\DbAdmin\Driver\Driver\DatabaseInterface;
use Lagdo\DbAdmin\Driver\Driver\GrammarInterface;
use Lagdo\DbAdmin\Driver\Driver\QueryInterface;
use Lagdo\DbAdmin\Driver\Driver\ServerInterface;
use Lagdo\DbAdmin\Driver\Driver\TableInterface;

interface DriverInterface extends ConfigInterface, ServerInterface, DatabaseInterface,
    TableInterface, QueryInterface, GrammarInterface, ConnectionInterface
{
    /**
     * Get the driver name
     *
     * @return string
     */
    public function name();

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
    public function openConnection(string $database, string $schema = ''): AbstractConnection;

    /**
     * Create a new connection to a database and a schema
     *
     * @param string $database  The database name
     * @param string $schema    The database schema
     *
     * @return AbstractConnection|null
     */
    public function newConnection(string $database, string $schema = ''): AbstractConnection|null;

    /**
     * @return AbstractConnection|null
     */
    public function connection(): AbstractConnection|null;

    /**
     * Close the connection to the server
     *
     * @return void
     */
    public function closeConnection(): void;

    /**
     * Check if connection has at least the given version
     *
     * @param string $version required version
     * @param string $mariaDb required MariaDB version
     *
     * @return bool
     */
    public function minVersion(string $version, string $mariaDb = ''): bool;

    /**
     * Get connection charset
     *
     * @return string
     */
    public function charset(): string;
}
