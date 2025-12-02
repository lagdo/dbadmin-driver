<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;

interface ConnectionInterface
{
    /**
     * Get the driver extension
     *
     * @return string
     */
    public function extension();

    /**
     * Get the server description
     *
     * @return string
     */
    public function serverInfo();

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
     * @param string $query
     * @param bool $unbuffered
     *
     * @return mixed
     */
    public function query(string $query, bool $unbuffered = false);

    /**
     * Get the number of rows affected by the last query
     *
     * @return integer
     */
    public function affectedRows();

    /**
     * Execute a query on the current database and fetch the specified field
     *
     * @param string $query
     * @param int $field
     *
     * @return mixed
     */
    public function result(string $query, int $field = -1);

    /**
     * Execute a query on the current database and store the result
     *
     * @param string $query
     *
     * @return bool
     */
    public function multiQuery(string $query);

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

    /**
     * Get the result saved by the multiQuery() method
     *
     * @return StatementInterface|bool
     */
    public function storedResult();

    /**
     * Get the next row set of the last query
     *
     * @return bool
     */
    public function nextResult();

    /**
     * Convert value returned by database to actual value
     *
     * @param string|resource|null $value
     * @param TableFieldEntity $field
     *
     * @return string
     */
    public function value($value, TableFieldEntity $field);

    /**
     * Explain select
     *
     * @param string $query
     *
     * @return StatementInterface|bool
     */
    public function explain(string $query);

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
