<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;

interface ConnectionInterface
{
    /**
     * Get the client
     *
     * @return mixed
     */
    public function client();

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
     * Connect to a database and a schema
     *
     * @param string $database  The database name
     * @param string $schema    The database schema
     *
     * @return bool
     */
    public function open(string $database, string $schema = '');

    /**
     * Sets the client character set
     *
     * @param string $charset
     *
     * @return bool
     */
    public function setCharset(string $charset);

    /**
     * Execute a query on the current database
     *
     * @param string $query
     * @param bool $unbuffered
     *
     * @return StatementInterface|bool
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
     * Execute a query on the current database and ??
     *
     * @param string $query
     *
     * @return bool
     */
    public function multiQuery(string $query);

    /**
     * Execute a query if it is of type "USE".
     *
     * @param string $query
     *
     * @return bool
     */
    public function execUseQuery(string $query);

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
     * @return string|null
     */
    public function value($value, TableFieldEntity $field);

    /**
     * Get warnings about the last command
     *
     * @return string
     */
    public function warnings();

    /**
     * Close the connection to the server
     *
     * @return void
     */
    public function close();

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
