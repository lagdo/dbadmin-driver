<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Entity\RoutineEntity;

interface ServerInterface
{
    /**
     * Get cached list of databases
     *
     * @param bool $flush
     *
     * @return array
     */
    public function databases(bool $flush);

    /**
     * Compute size of database
     *
     * @param string $database
     *
     * @return int
     */
    public function databaseSize(string $database);

    /**
     * Get database collation
     *
     * @param string $database
     * @param array $collations
     *
     * @return string
     */
    public function databaseCollation(string $database, array $collations);

    /**
     * Get supported engines
     *
     * @return array
     */
    public function engines();

    /**
     * Get sorted grouped list of collations
     *
     * @return array
     */
    public function collations();

    /**
     * Find out if database is information_schema
     *
     * @param string $database
     *
     * @return bool
     */
    public function isInformationSchema(string $database);

    /**
     * Create database
     *
     * @param string $database
     * @param string $collation
     *
     * @return boolean
     */
    public function createDatabase(string $database, string $collation) ;

    /**
     * Drop databases
     *
     * @param array $databases
     *
     * @return bool
     */
    public function dropDatabases(array $databases);

    /**
     * Rename database from DB
     *
     * @param string $name New name
     * @param string $collation
     *
     * @return bool
     */
    public function renameDatabase(string $name, string $collation);

    /**
     * Get list of available routine languages
     *
     * @return array
     */
    public function routineLanguages() ;

    /**
     * Get server variables
     *
     * @return array
     */
    public function variables();

    /**
     * Get status variables
     *
     * @return array
     */
    public function statusVariables();

    /**
     * Get process list
     *
     * @return array
     */
    public function processes();

    /**
     * Kill a process
     *
     * @param int
     *
     * @return bool
     */
    // public function killProcess($val);

    /**
     * Get maximum number of connections
     *
     * @return int
     */
    // public function maxConnections();
}
