<?php

namespace Lagdo\Adminer\Driver\Db;

interface ConnectionInterface
{
    /**
     * Get the client
     *
     * @return mixed
     */
    public function getClient();

    /**
     * Set the current database
     *
     * @param string $database
     *
     * @return boolean
     */
    public function select_db($database);

    /**
     * Sets the client character set
     * @param string
     * @return bool
     */
    public function set_charset($charset);

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
     * Convert value returned by database to actual value
     * @param string
     * @param array
     * @return string
     */
    public function value($val, $field);

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
