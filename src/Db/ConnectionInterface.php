<?php

namespace Lagdo\DbAdmin\Driver\Db;

interface ConnectionInterface extends DriverConnectionInterface
{
    /**
     * Get the client
     *
     * @return mixed
     */
    public function client();

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
}
