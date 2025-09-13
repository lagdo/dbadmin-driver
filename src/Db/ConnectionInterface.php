<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Driver\ConnectionInterface as DriverConnectionInterface;

interface ConnectionInterface extends DriverConnectionInterface
{
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
     * Close the connection to the server
     *
     * @return void
     */
    public function close();

    /**
     * Execute a query if it is of type "USE".
     *
     * @param string $query
     *
     * @return void
     */
    public function execUseQuery(string $query);

    /**
     * Get warnings about the last command
     *
     * @return string
     */
    public function warnings();

    /**
     * Set the error message
     *
     * @param string $error
     *
     * @return void
     */
    public function setError(string $error = '');

    /**
     * Get the raw error message
     *
     * @return string
     */
    public function error(): string;

    /**
     * Check if the last query returned an error message
     *
     * @return bool
     */
    public function hasError();

    /**
     * Set the error number
     *
     * @param int $errno
     *
     * @return void
     */
    public function setErrno(int $errno);

    /**
     * Get the full error message
     *
     * @return string
     */
    public function errorMessage();
}
