<?php

namespace Lagdo\DbAdmin\Driver\Db;

interface ConnectionInterface
{
    /**
     * Get the client
     *
     * @return mixed
     */
    public function getClient();

    /**
     * Get the server description
     *
     * @return string
     */
    public function getServerInfo();

    /**
     * Set the current database
     *
     * @param string $database
     *
     * @return boolean
     */
    public function selectDatabase($database);

    /**
     * Sets the client character set
     * @param string
     * @return bool
     */
    public function setCharset($charset);

    /**
     * Execute a query on the current database
     *
     * @param string $query
     * @param boolean $unbuffered
     *
     * @return mixed
     */
    public function query($query, $unbuffered = false);

    /**
     * Execute a query on the current database and fetch the specified field
     *
     * @param string $query
     * @param mixed $field
     *
     * @return mixed
     */
    public function result($query, $field = 1);

    /**
     * Get the next row set of the last query
     *
     * @return mixed
     */
    public function nextResult();

    /**
     * Execute a query on the current database and ??
     *
     * @param string $query
     *
     * @return mixed
     */
    public function multiQuery($query);

    /**
     * Get the result saved by the multiQuery() method
     *
     * @param mixed $result
     *
     * @return mixed
     */
    public function storedResult($result = null);

    /**
     * Convert value returned by database to actual value
     * @param string
     * @param array
     * @return string
     */
    public function value($val, $field);

    /**
     * Get warnings about the last command
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
    public function quote($string);

    /**
     * Get the default field number
     *
     * @return integer
     */
    public function defaultField();

    /**
     * Return a quoted string
     *
     * @param string $string
     *
     * @return string
     */
    public function quoteBinary($string);

    /**
     * Open a connection to a server
     *
     * @param string $server    The server address, name or uri
     * @param array  $options   The connection options
     *
     * @return mixed
     */
    public function open($server, array $options);
}
