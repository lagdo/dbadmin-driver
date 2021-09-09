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
     * Open a connection to a server
     *
     * @param string $server    The server address, name or uri
     * @param array  $options   The connection options
     *
     * @return boolean
     */
    public function open(string $server, array $options);

    /**
     * Set the current database
     *
     * @param string $database
     *
     * @return boolean
     */
    public function selectDatabase(string $database);

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
     * Execute a query on the current database and fetch the specified field
     *
     * @param string $query
     * @param int $field
     *
     * @return mixed
     */
    public function result(string $query, int $field = 0);

    /**
     * Get the next row set of the last query
     *
     * @return bool
     */
    public function nextResult();

    /**
     * Execute a query on the current database and ??
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
     * Convert value returned by database to actual value
     *
     * @param string $val
     * @param TableFieldEntity $field
     *
     * @return string
     */
    public function value(?string $val, TableFieldEntity $field);

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
     * Get the default field number
     *
     * @return int
     */
    public function defaultField();

    /**
     * Return a quoted string
     *
     * @param string $string
     *
     * @return string
     */
    public function quoteBinary(string $string);
}
