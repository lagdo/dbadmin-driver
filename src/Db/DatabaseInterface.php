<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\Entity\RoutineEntity;

use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;

interface DatabaseInterface
{
    /**
     * Create table
     *
     * @param TableEntity $tableAttrs
     *
     * @return bool
     */
    public function createTable(TableEntity $tableAttrs);

    /**
     * Alter table
     *
     * @param string $table
     * @param TableEntity $tableAttrs
     *
     * @return bool
     */
    public function alterTable(string $table, TableEntity $tableAttrs);

    /**
     * Alter indexes
     *
     * @param string $table Escaped table name
     * @param array $alter  Indexes to alter. Array of IndexEntity.
     * @param array $drop   Indexes to drop. Array of IndexEntity.
     *
     * @return bool
     */
    public function alterIndexes(string $table, array $alter, array $drop);

    /**
     * Get tables list
     *
     * @return array
     */
    public function tables();

    /**
     * Get sequences list
     *
     * @return array
     */
    public function sequences();

    /**
     * Count tables in all databases
     *
     * @param array $databases
     *
     * @return array
     */
    public function countTables(array $databases);

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
     * Get events
     *
     * @return array
     */
    public function events();

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
     * Get routine signature
     *
     * @param string $name
     * @param array $row result of routine()
     *
     * @return string
     */
    public function routineId(string $name, array $row);
}
