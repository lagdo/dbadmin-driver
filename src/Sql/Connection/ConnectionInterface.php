<?php

namespace Lagdo\DbAdmin\Driver\Sql\Connection;

use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;

interface ConnectionInterface
{
    /**
     * Get the driver extension
     *
     * @return string
     */
    public function extension(): string;

    /**
     * Get the database flavor
     *
     * @return string
     */
    public function flavor(): string;

    /**
     * Get the server description
     *
     * @return string
     */
    public function serverInfo(): string;

    /**
     * Return a quoted string
     *
     * @param string $string
     *
     * @return string
     */
    public function quote(string $string): string;

    /**
     * Return a quoted string
     *
     * @param string $string
     *
     * @return string
     */
    public function quoteBinary(string $string): string;

    /**
     * @param string $query
     * @param bool $unbuffered
     *
     * @return QueryResultInterface
     */
    public function executeQuery(string $query, bool $unbuffered = false): QueryResultInterface;

    /**
     * Get the number of rows affected by the last query
     *
     * @return integer
     */
    public function affectedRows(): int;

    /**
     * Convert value returned by database to actual value
     *
     * @param string|resource|null $value
     * @param ColumnDto $column
     *
     * @return mixed
     */
    public function convertValue(mixed $value, ColumnDto $column): mixed;

    /**
     * Create a prepared statement
     *
     * @param string $query
     *
     * @return PreparedStatement
     */
    public function prepareStatement(string $query): PreparedStatement;

    /**
     * Execute a prepared statement
     *
     * @param PreparedStatement $preparedStatement
     * @param array $values
     *
     * @return QueryResultInterface
     */
    public function executeStatement(PreparedStatement $preparedStatement,
        array $values): QueryResultInterface;

    /**
     * Execute a query on the current database and store the result
     *
     * @param string $query
     *
     * @return QueryResultInterface
     */
    public function executeMultiQuery(string $query): QueryResultInterface;

    /**
     * Get the current rowset in the multiQuery() result
     *
     * @param QueryResultInterface $result
     *
     * @return QueryResultInterface
     */
    public function readRowset(QueryResultInterface $result): QueryResultInterface;

    /**
     * Move to the next rowset of the last multiQuery() result
     *
     * @param QueryResultInterface $result
     *
     * @return bool
     */
    public function nextRowset(QueryResultInterface $result): bool;

    /**
     * Explain select
     *
     * @param string $query
     *
     * @return QueryResultInterface|bool
     */
    public function explain(string $query): QueryResultInterface|bool;

    /**
     * Get the raw error message
     *
     * @return string
     */
    public function error(): string;

    /**
     * @return bool
     */
    public function hasError(): bool;

    /**
     * @return string
     */
    public function errorMessage(): string;
}
