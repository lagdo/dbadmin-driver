<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\Entity\IndexEntity;
use Lagdo\DbAdmin\Driver\Entity\ForeignKeyEntity;
use Lagdo\DbAdmin\Driver\Entity\TriggerEntity;
use Lagdo\DbAdmin\Driver\Entity\RoutineEntity;

use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;

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
     * Get tables list
     *
     * @return array
     */
    public function tables();

    /**
     * Count tables in all databases
     *
     * @param array $databases
     *
     * @return array
     */
    public function countTables(array $databases);

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
     * @return string|boolean
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
     * Get information about stored routine
     *
     * @param string $name
     * @param string $type "FUNCTION" or "PROCEDURE"
     *
     * @return RoutineEntity
     */
    public function routine(string $name, string $type);

    /**
     * Get list of routines
     *
     * @return array
     */
    public function routines();

    /**
     * Get list of available routine languages
     *
     * @return array
     */
    public function routineLanguages() ;

    /**
     * Get routine signature
     *
     * @param string $name
     * @param array $row result of routine()
     *
     * @return string
     */
    public function routineId(string $name, array $row);

    /**
     * Get user defined types
     *
     * @return array
     */
    public function userTypes() ;

    /**
     * Get existing schemas
     *
     * @return array
     */
    public function schemas();

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






    /**
     * Drop views
     *
     * @param array $views
     *
     * @return bool
     */
    public function dropViews(array $views);

    /**
     * Truncate tables
     *
     * @param array $tables
     *
     * @return bool
     */
    public function truncateTables(array $tables);

    /**
     * Drop tables
     *
     * @param array $tables
     *
     * @return bool
     */
    public function dropTables(array $tables);

    /**
     * Move tables to other schema
     *
     * @param array $tables
     * @param array $views
     * @param string $target
     *
     * @return bool
     */
    public function moveTables(array $tables, array $views, string $target);

    /**
     * Copy tables to other schema
     *
     * @param array $tables
     * @param array $views
     * @param string $target
     *
     * @return bool
     */
    public function copyTables(array $tables, array $views, string $target);
}
