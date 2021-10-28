<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Entity\RoutineEntity;

use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;

interface DatabaseInterface
{
    /**
     * Create or alter table
     *
     * @param string $table "" to create
     * @param string $name new name
     * @param array $fields of array($orig, $process_field, $after)
     * @param array $foreign of strings
     * @param string $comment
     * @param string $engine
     * @param string $collation
     * @param int $autoIncrement number
     * @param string $partitioning
     *
     * @return bool
     */
    public function alterTable(string $table, string $name, array $fields, array $foreign, string $comment,
        string $engine, string $collation, int $autoIncrement, string $partitioning);

    /**
     * Alter indexes
     *
     * @param string $table     Escaped table name
     * @param array $alter      array("index type", "name", array("column definition", ...)) or array("index type", "name", "DROP")
     *
     * @return bool
     */
    public function alterIndexes(string $table, array $alter);

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
