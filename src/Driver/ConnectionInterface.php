<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Lagdo\DbAdmin\Driver\Db\ConnectionInterface as DbConnectionInterface;
use Lagdo\DbAdmin\Driver\Db\PreparedStatement;
use Lagdo\DbAdmin\Driver\Db\StatementInterface;

/**
 * Connection functions implemented in the Driver class.
 */
interface ConnectionInterface extends DbConnectionInterface
{
    /**
     * Create a connection to a server
     *
     * @param array $options
     *
     * @return DbConnectionInterface|null
     */
    public function createConnection(array $options);

    /**
     * Connect to a database and a schema
     *
     * @param string $database  The database name
     * @param string $schema    The database schema
     *
     * @return DbConnectionInterface
     */
    public function open(string $database, string $schema = ''): DbConnectionInterface;

    /**
     * Create a new connection to a database and a schema
     *
     * @param string $database  The database name
     * @param string $schema    The database schema
     *
     * @return DbConnectionInterface|null
     */
    public function connectToDatabase(string $database, string $schema = ''): DbConnectionInterface|null;

    /**
     * @return DbConnectionInterface|null
     */
    public function connection(): DbConnectionInterface|null;

    /**
     * Close the connection to the server
     *
     * @return void
     */
    public function close(): void;

    /**
     * Execute and remember query
     *
     * @param string $query
     *
     * @return StatementInterface|bool
     */
    public function execute(string $query);

    /**
     * Apply command to all array items
     *
     * @param string $query
     * @param array $tables
     * @param callback|null $escape
     *
     * @return bool
     */
    public function applyQueries(string $query, array $tables, $escape = null);

    /**
     * Get list of values from database
     *
     * @param string $query
     * @param int $column
     *
     * @return array
     */
    public function values(string $query, int $column = 0);

    /**
     * Get list of values from database
     *
     * @param string $query
     * @param string $column
     *
     * @return array
     */
    public function colValues(string $query, string $column);

    /**
     * Get keys from first column and values from second
     *
     * @param string $query
     * @param int $keyColumn
     * @param int $valueColumn
     *
     * @return array
     */
    public function keyValues(string $query, int $keyColumn = 0, int $valueColumn = 1);

    /**
     * Get all rows of result
     *
     * @param string $query
     *
     * @return array
     */
    public function rows(string $query): array;

    /**
     * Begin transaction
     *
     * @return bool
     */
    public function begin();

    /**
     * Commit transaction
     *
     * @return bool
     */
    public function commit();

    /**
     * Rollback transaction
     *
     * @return bool
     */
    public function rollback();

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

    /**
     * Create a prepared statement
     *
     * @param string $query
     *
     * @return void
     */
    public function prepareStatement(string $query): PreparedStatement;

    /**
     * Execute a prepared statement
     *
     * @param PreparedStatement $statement
     * @param array $values
     *
     * @return StatementInterface|bool
     */
    public function executeStatement(PreparedStatement $statement,
        array $values): ?StatementInterface;
}
