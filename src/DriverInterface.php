<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;
use Lagdo\DbAdmin\Driver\Db\ServerInterface;
use Lagdo\DbAdmin\Driver\Db\DatabaseInterface;
use Lagdo\DbAdmin\Driver\Db\TableInterface;
use Lagdo\DbAdmin\Driver\Db\QueryInterface;
use Lagdo\DbAdmin\Driver\Db\GrammarInterface;
use Lagdo\DbAdmin\Driver\Db\StatementInterface;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;

interface DriverInterface extends ConfigInterface, ServerInterface,
    DatabaseInterface, TableInterface, QueryInterface, GrammarInterface
{
    /**
     * Get the driver name
     *
     * @return string
     */
    public function name();

    /**
     * Create a connection to the server, based on the config and available packages
     *
     * @return ConnectionInterface|null
     */
    public function createConnection();

    /**
     * Connect to a database and a schema
     *
     * @param string $database  The database name
     * @param string $schema    The database schema
     *
     * @return void
     */
    public function connect(string $database, string $schema);

    /**
     * Check if a feature is supported
     *
     * @param string $feature
     *
     * @return bool
     */
    public function support(string $feature);

    /**
     * Check if connection has at least the given version
     *
     * @param string $version required version
     * @param string $mariaDb required MariaDB version
     *
     * @return bool
     */
    public function minVersion(string $version, string $mariaDb = '');

    /**
     * Get connection charset
     *
     * @return string
     */
    public function charset();

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
     * Get SET NAMES if utf8mb4 might be needed
     *
     * @param string $create
     *
     * @return string
     */
    public function setUtf8mb4(string $create);

    /**
     * Set the error message
     *
     * @param string $error
     *
     * @return void
     */
    public function setError(string $error = '');

    /**
     * Get the raw error message
     *
     * @return string
     */
    public function error();

    /**
     * Check if the last query returned an error message
     *
     * @return bool
     */
    public function hasError();

    /**
     * Set the error number
     *
     * @param int $errno
     *
     * @return void
     */
    public function setErrno(int $errno);

    /**
     * Get the last error number
     *
     * @return string
     */
    public function errno();

    /**
     * Check if the last query returned an error number
     *
     * @return bool
     */
    public function hasErrno();

    /**
     * Get the full error message
     *
     * @return string
     */
    public function errorMessage();

    /**
     * Get the number of rows affected by the last query
     *
     * @return integer
     */
    public function affectedRows();

    /**
     * Execute and remember query
     *
     * @param string $query
     *
     * @return StatementInterface|bool
     */
    public function execute(string $query);

    /**
     * Get the remembered queries
     *
     * @return array
     */
    public function queries();

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
     * Convert value returned by database to actual value
     *
     * @param string|resource|null $value
     * @param TableFieldEntity $field
     *
     * @return string|null
     */
    public function value($value, TableFieldEntity $field);

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
    public function rows(string $query);

    /**
     * Return a quoted string
     *
     * @param string $string
     *
     * @return string
     */
    public function quote(string $string);

    /**
     * Return a quoted string
     *
     * @param string $string
     *
     * @return string
     */
    public function quoteBinary(string $string);
}
