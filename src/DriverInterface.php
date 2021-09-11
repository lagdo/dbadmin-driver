<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Entity\ConfigEntity;

use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;
use Lagdo\DbAdmin\Driver\Db\ServerInterface;
use Lagdo\DbAdmin\Driver\Db\TableInterface;
use Lagdo\DbAdmin\Driver\Db\QueryInterface;
use Lagdo\DbAdmin\Driver\Db\GrammarInterface;

interface DriverInterface
{
    /**
     * Get the driver name
     *
     * @return string
     */
    public function name();

    /**
     * Get driver config
     *
     * @return ConfigEntity
     */
    public function config();

    /**
     * Get the Adminer version
     *
     * @return string
     */
    public function version();

    /**
     * Get the driver options
     *
     * @param string $name The option name
     *
     * @return mixed
     */
    public function options(string $name = '');

    /**
     * Create a connection to the server, based on the config and available packages
     *
     * @return ConnectionInterface|null
     */
    public function createConnection();

    /**
     * Connect to a database and a schema
     *
     * @param string $database  The database name
     * @param string $schema    The database schema
     *
     * @return void
     */
    public function connect(string $database, string $schema);

    /**
     * Select the database and schema
     *
     * @return string
     */
    public function selectedDatabase();

    /**
     * Select the database and schema
     *
     * @return string
     */
    public function selectedSchema();

    /**
     * Check whether a feature is supported
     *
     * @param string $feature
     *
     * @return bool
     */
    public function support(string $feature);

    /**
     * Check if connection has at least the given version
     *
     * @param string $version required version
     * @param string $mariaDb required MariaDB version
     * @param ConnectionInterface|null $connection
     *
     * @return bool
     */
    public function minVersion(string $version, string $mariaDb = "", ConnectionInterface $connection = null);

    /**
     * Get connection charset
     *
     * @return string
     */
    public function charset();

    /**
     * Get SET NAMES if utf8mb4 might be needed
     *
     * @param string $create
     *
     * @return string
     */
    public function setUtf8mb4(string $create);

    /**
     * Get regular expression to match numeric types
     *
     * @return string
     */
    public function numberRegex();
}
