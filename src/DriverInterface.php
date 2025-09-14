<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Db\StatementInterface;
use Lagdo\DbAdmin\Driver\Driver\ConfigInterface;
use Lagdo\DbAdmin\Driver\Driver\ConnectionInterface;
use Lagdo\DbAdmin\Driver\Driver\ServerInterface;
use Lagdo\DbAdmin\Driver\Driver\DatabaseInterface;
use Lagdo\DbAdmin\Driver\Driver\TableInterface;
use Lagdo\DbAdmin\Driver\Driver\QueryInterface;
use Lagdo\DbAdmin\Driver\Driver\GrammarInterface;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Closure;

interface DriverInterface extends ConfigInterface, ConnectionInterface,
    ServerInterface, DatabaseInterface, TableInterface, QueryInterface,
    GrammarInterface
{
    /**
     * Get the driver name
     *
     * @return string
     */
    public function name();

    /**
     * Connect to a database and a schema
     *
     * @param string $database  The database name
     * @param string $schema    The database schema
     *
     * @return Db\ConnectionInterface
     */
    public function open(string $database, string $schema = ''): Db\ConnectionInterface;

    /**
     * Create a new connection to a database and a schema
     *
     * @param string $database  The database name
     * @param string $schema    The database schema
     *
     * @return Db\ConnectionInterface
     */
    public function newConnection(string $database, string $schema = ''): Db\ConnectionInterface;

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
     * Get the raw error message
     *
     * @return string
     */
    public function error(): string;

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
     * @param Closure $callback
     *
     * @return void
     */
    public function addQueryCallback(Closure $callback): void;

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
     * Execute a query if it is of type "USE".
     *
     * @param string $query
     *
     * @return void
     */
    public function execUseQuery(string $query);

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

    /**
     * Escape or unescape string to use inside form []
     *
     * @param string $idf
     * @param bool $back
     *
     * @return string
     */
    public function bracketEscape(string $idf, bool $back = false): string;

    /**
     * Escape column key used in where()
     *
     * @param string
     *
     * @return string
     */
    public function escapeKey(string $key): string;

    /**
     * Filter length value including enums
     *
     * @param string $length
     *
     * @return string
     */
    public function processLength(string $length): string;

    /**
     * Create SQL string from field
     *
     * @param TableFieldEntity $field Basic field information
     * @param TableFieldEntity $typeField Information about field type
     *
     * @return array
     */
    public function processField(TableFieldEntity $field, TableFieldEntity $typeField): array;
}
