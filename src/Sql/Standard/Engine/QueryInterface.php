<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Engine;

use Lagdo\DbAdmin\Driver\Sql\Connection\QueryResultInterface;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;
use Closure;

interface QueryInterface
{
    /**
     * Execute a query
     *
     * @param string $query
     *
     * @return bool
     */
    public function execute(string $query): bool;

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
     * @return QueryResultInterface
     */
    public function select(string $table, array $select, array $where, array $group = [],
        array $order = [], int $limit = 1, int $page = 0): QueryResultInterface;

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
     * @param Closure|null $escape
     *
     * @return bool
     */
    public function applyQueries(string $query, array $tables, Closure|null $escape = null): bool;

    /**
     * Get list of values from database
     *
     * @param string $query
     * @param string|int $column
     *
     * @return array
     */
    public function columnValues(string $query, string|int $column = -1): array;

    /**
     * Get a value from database
     * This is the get_val() function in Adminer.
     *
     * @param string $query
     * @param string|int $column
     *
     * @return mixed
     */
    public function columnValue(string $query, string|int $column = -1): mixed;

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
