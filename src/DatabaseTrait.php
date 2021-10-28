<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Entity\ConfigEntity;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\Entity\ForeignKeyEntity;
use Lagdo\DbAdmin\Driver\Entity\TriggerEntity;
use Lagdo\DbAdmin\Driver\Entity\RoutineEntity;

use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;

trait DatabaseTrait
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
    public function alterTable(string $table, string $name, array $fields, array $foreign,
        string $comment, string $engine, string $collation, int $autoIncrement, string $partitioning)
    {
        return $this->database->alterTable($table, $name, $fields, $foreign, $comment,
            $engine, $collation, $autoIncrement, $partitioning);
    }

    /**
     * Alter indexes
     *
     * @param string $table     Escaped table name
     * @param array $alter      array("index type", "name", array("column definition", ...)) or array("index type", "name", "DROP")
     *
     * @return bool
     */
    public function alterIndexes(string $table, array $alter)
    {
        return $this->database->alterIndexes($table, $alter);
    }

    /**
     * Get tables list
     *
     * @return array
     */
    public function tables()
    {
        return $this->database->tables();
    }

    /**
     * Get sequences list
     *
     * @return array
     */
    public function sequences()
    {
        return $this->database->sequences();
    }

    /**
     * Count tables in all databases
     *
     * @param array $databases
     *
     * @return array
     */
    public function countTables(array $databases)
    {
        return $this->database->countTables($databases);
    }

    /**
     * Drop views
     *
     * @param array $views
     *
     * @return bool
     */
    public function dropViews(array $views)
    {
        return $this->database->dropViews($views);
    }

    /**
     * Truncate tables
     *
     * @param array $tables
     *
     * @return bool
     */
    public function truncateTables(array $tables)
    {
        return $this->database->truncateTables($tables);
    }

    /**
     * Drop tables
     *
     * @param array $tables
     *
     * @return bool
     */
    public function dropTables(array $tables)
    {
        return $this->database->dropTables($tables);
    }

    /**
     * Move tables to other schema
     *
     * @param array $tables
     * @param array $views
     * @param string $target
     *
     * @return bool
     */
    public function moveTables(array $tables, array $views, string $target)
    {
        return $this->database->moveTables($tables, $views, $target);
    }

    /**
     * Copy tables to other schema
     *
     * @param array $tables
     * @param array $views
     * @param string $target
     *
     * @return bool
     */
    public function copyTables(array $tables, array $views, string $target)
    {
        return $this->database->copyTables($tables, $views, $target);
    }

    /**
     * Get user defined types
     *
     * @return array
     */
    public function userTypes()
    {
        return $this->database->userTypes();
    }

    /**
     * Get existing schemas
     *
     * @return array
     */
    public function schemas()
    {
        return $this->database->schemas();
    }

    /**
     * Get events
     *
     * @return array
     */
    public function events()
    {
        return $this->database->events();
    }

    /**
     * Get information about stored routine
     *
     * @param string $name
     * @param string $type "FUNCTION" or "PROCEDURE"
     *
     * @return RoutineEntity
     */
    public function routine(string $name, string $type)
    {
        return $this->database->routine($name, $type);
    }

    /**
     * Get list of routines
     *
     * @return array
     */
    public function routines()
    {
        return $this->database->routines();
    }
}
