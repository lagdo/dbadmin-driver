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
     * Create table
     *
     * @param TableEntity $tableAttrs
     *
     * @return bool
     */
    public function createTable(TableEntity $tableAttrs)
    {
        return $this->database->createTable($tableAttrs);
    }

    /**
     * Alter table
     *
     * @param string $table
     * @param TableEntity $tableAttrs
     *
     * @return bool
     */
    public function alterTable(string $table, TableEntity $tableAttrs)
    {
        return $this->database->alterTable($table, $tableAttrs);
    }

    /**
     * Alter indexes
     *
     * @param string $table Escaped table name
     * @param array $alter  Indexes to alter. Array of IndexEntity.
     * @param array $drop   Indexes to drop. Array of IndexEntity.
     *
     * @return bool
     */
    public function alterIndexes(string $table, array $alter, array $drop)
    {
        return $this->database->alterIndexes($table, $alter, $drop);
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
