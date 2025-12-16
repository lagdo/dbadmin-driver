<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Lagdo\DbAdmin\Driver\Db\StatementInterface;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Exception;

interface QueryInterface
{
    /**
     * Select data from table
     *
     * @param string $table
     * @param array $select Result of processSelectColumns()[0]
     * @param array $where Result of processSelectWhere()
     * @param array $group Result of processSelectColumns()[1]
     * @param array $order Result of processSelectOrder()
     * @param int $limit Result of processSelectLimit()
     * @param int $page Index of page starting at zero
     *
     * @return StatementInterface|bool
     */
    public function select(string $table, array $select, array $where, array $group = [],
        array $order = [], int $limit = 1, int $page = 0): StatementInterface|bool;

    /**
     * Insert data into table
     *
     * @param string $table
     * @param array $values Escaped columns in keys, quoted data in values
     *
     * @return bool
     */
    public function insert(string $table, array $values): bool;

    /**
     * Update data in table
     *
     * @param string $table
     * @param array $values Escaped columns in keys, quoted data in values
     * @param string $queryWhere " WHERE ..."
     * @param int $limit 0 or 1
     *
     * @return bool
     */
    public function update(string $table, array $values, string $queryWhere, int $limit = 0): bool;

    /**
     * Delete data from table
     *
     * @param string $table
     * @param string $queryWhere " WHERE ..."
     * @param int $limit 0 or 1
     *
     * @return bool
     */
    public function delete(string $table, string $queryWhere, int $limit = 0): bool;

    /**
     * Insert or update data in table
     *
     * @param string $table
     * @param array $rows
     * @param array $primary of arrays with escaped columns in keys and quoted data in values
     *
     * @return bool
     */
    public function insertOrUpdate(string $table, array $rows, array $primary): bool;

    /**
     * Get last auto increment ID
     *
     * @return string
     */
    public function lastAutoIncrementId(): string;

    /**
     * Return query with a timeout
     *
     * @param string $query
     * @param int $timeout In seconds
     *
     * @return string|null
     */
    public function slowQuery(string $query, int $timeout): string|null;

    /**
     * Execute query
     *
     * @param string $query
     * @param bool $execute
     * @param bool $failed
     *
     * @return bool
     * @throws Exception
     */
    public function executeQuery(string $query, bool $execute = true,
        bool $failed = false/*, string $time = ''*/): bool;

    /**
     * Create SQL condition from parsed query string
     *
     * @param array $where Parsed query string
     * @param array $fields
     *
     * @return string
     */
    public function where(array $where, array $fields = []): string;

    /**
     * Get approximate number of rows
     *
     * @param TableEntity $tableStatus
     * @param array $where
     *
     * @return int|null
     */
    public function countRows(TableEntity $tableStatus, array $where): int|null;

    /**
     * Convert column to be searchable
     *
     * @param string $idf escaped column name
     * @param array $value array("op" => , "val" => )
     * @param TableFieldEntity $field
     *
     * @return string
     */
    public function convertSearch(string $idf, array $value, TableFieldEntity $field): string;

    /**
     * Apply command to all array items
     *
     * @param string $query
     * @param array $tables
     * @param callback|null $escape
     *
     * @return bool
     */
    public function applyQueries(string $query, array $tables, $escape = null): bool;

    /**
     * Get list of values from database
     *
     * @param string $query
     * @param int $column
     *
     * @return array
     */
    public function values(string $query, int $column = 0): array;

    /**
     * Get list of values from database
     *
     * @param string $query
     * @param string $column
     *
     * @return array
     */
    public function colValues(string $query, string $column): array;

    /**
     * Get keys from first column and values from second
     *
     * @param string $query
     * @param bool $setKeys
     *
     * @return array
     */
    public function keyValues(string $query, bool $setKeys = true): array;

    /**
     * Get all rows of result
     *
     * @param string $query
     *
     * @return array
     */
    public function rows(string $query): array;

    /**
     * Execute and remember query
     *
     * @param string $query
     *
     * @return StatementInterface|bool
     */
    public function execute(string $query): StatementInterface|bool;

    /**
     * Begin transaction
     *
     * @return bool
     */
    public function begin(): bool;

    /**
     * Commit transaction
     *
     * @return bool
     */
    public function commit(): bool;

    /**
     * Rollback transaction
     *
     * @return bool
     */
    public function rollback(): bool;

    /**
     * Get view SELECT
     *
     * @param string $name
     *
     * @return array array("select" => )
     */
    public function view(string $name): array;
}
