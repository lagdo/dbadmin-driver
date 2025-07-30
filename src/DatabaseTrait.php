<?php

namespace Lagdo\DbAdmin\Driver;

use Exception;
use Lagdo\DbAdmin\Driver\Db\DatabaseInterface;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\Entity\RoutineEntity;

trait DatabaseTrait
{
    /**
     * @var DatabaseInterface
     */
    protected $database;

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
     * Create a view
     *
     * @param array $values The view values
     *
     * @return bool
     * @throws Exception
     */
    public function createView(array $values)
    {
        return $this->database->createView($values);
    }

    /**
     * Update a view
     *
     * @param string $view The view name
     * @param array $values The view values
     *
     * @return string
     * @throws Exception
     */
    public function updateView(string $view, array $values): string
    {
        return $this->database->updateView($view, $values);
    }

    /**
     * Drop a view
     *
     * @param string $view The view name
     *
     * @return bool
     * @throws Exception
     */
    public function dropView(string $view): bool
    {
        return $this->database->dropView($view);
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

    /**
     * Get routine signature
     *
     * @param string $name
     * @param array $row result of routine()
     *
     * @return string
     */
    public function routineId(string $name, array $row)
    {
        return $this->database->routineId($name, $row);
    }
}
