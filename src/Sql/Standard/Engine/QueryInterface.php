<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Engine;

use Lagdo\DbAdmin\Driver\Sql\Connection\StatementInterface;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;
use Exception;

interface QueryInterface
{
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
     * @param array<ColumnDto> $columns
     *
     * @return string
     */
    public function where(array $where, array $columns = []): string;

    /**
     * Get all rows of result
     *
     * @param string $query
     *
     * @return array
     */
    public function rows(string $query): array;

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
     * @param string|int $column
     *
     * @return array
     */
    public function columnValues(string $query, string|int $column = 0): array;

    /**
     * Get a value from database
     *
     * @param string $query
     * @param string|int $column
     *
     * @return mixed
     */
    public function columnValue(string $query, string|int $column = 0): mixed;

    /**
     * Get keys from first column and values from second
     *
     * @param string $query
     * @param bool $setKeys
     *
     * @return array
     */
    public function keyValues(string $query, bool $setKeys = true): array;
}
