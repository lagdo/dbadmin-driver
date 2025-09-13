<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Lagdo\DbAdmin\Driver\Db\StatementInterface;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;

/**
 * Functions of the ConnectionInterface interface that are
 * included in the DriverInterface interface.
 */
interface ConnectionInterface
{
    /**
     * Get the server description
     *
     * @return string
     */
    public function serverInfo();

    /**
     * Get the driver extension
     *
     * @return string
     */
    public function extension();

    /**
     * Sets the client character set
     *
     * @param string $charset
     *
     * @return void
     */
    public function setCharset(string $charset);

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
     * Get the number of rows affected by the last query
     *
     * @return integer
     */
    public function affectedRows();

    /**
     * Execute a query on the current database and store the result
     *
     * @param string $query
     *
     * @return bool
     */
    public function multiQuery(string $query);

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
     * Execute a query on the current database and fetch the specified field
     *
     * @param string $query
     * @param int $field
     *
     * @return mixed
     */
    public function result(string $query, int $field = -1);

    /**
     * @return int
     */
    public function defaultField(): int;

    /**
     * Explain select
     *
     * @param string $query
     *
     * @return StatementInterface|bool
     */
    public function explain(string $query);
}
