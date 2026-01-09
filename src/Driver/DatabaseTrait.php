<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Lagdo\DbAdmin\Driver\Dto\RoutineDto;
use Lagdo\DbAdmin\Driver\Dto\RoutineInfoDto;
use Lagdo\DbAdmin\Driver\Dto\TableDto;
use Lagdo\DbAdmin\Driver\Dto\TableFieldDto;
use Lagdo\DbAdmin\Driver\Dto\UserTypeDto;
use Exception;

trait DatabaseTrait
{
    /**
     * @var DatabaseInterface
     */
    abstract protected function _database(): DatabaseInterface;

    /**
     * Alter indexes
     *
     * @param string $table Escaped table name
     * @param array $alter  Indexes to alter. Array of IndexDto.
     * @param array $drop   Indexes to drop. Array of IndexDto.
     *
     * @return bool
     */
    public function alterIndexes(string $table, array $alter, array $drop): bool
    {
        return $this->_database()->alterIndexes($table, $alter, $drop);
    }

    /**
     * Get tables list
     *
     * @return array
     */
    public function tables(): array
    {
        return $this->_database()->tables();
    }

    /**
     * Get sequences list
     *
     * @return array
     */
    public function sequences(): array
    {
        return $this->_database()->sequences();
    }

    /**
     * Count tables in all databases
     *
     * @param array $databases
     *
     * @return array
     */
    public function countTables(array $databases): array
    {
        return $this->_database()->countTables($databases);
    }

    /**
     * Drop views
     *
     * @param array $views
     *
     * @return bool
     */
    public function dropViews(array $views): bool
    {
        return $this->_database()->dropViews($views);
    }

    /**
     * Truncate tables
     *
     * @param array $tables
     *
     * @return bool
     */
    public function truncateTables(array $tables): bool
    {
        return $this->_database()->truncateTables($tables);
    }

    /**
     * Drop tables
     *
     * @param array $tables
     *
     * @return bool
     */
    public function dropTables(array $tables): bool
    {
        return $this->_database()->dropTables($tables);
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
    public function moveTables(array $tables, array $views, string $target): bool
    {
        return $this->_database()->moveTables($tables, $views, $target);
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
    public function copyTables(array $tables, array $views, string $target): bool
    {
        return $this->_database()->copyTables($tables, $views, $target);
    }

    /**
     * Create a view
     *
     * @param array $values The view values
     *
     * @return bool
     * @throws Exception
     */
    public function createView(array $values): bool
    {
        return $this->_database()->createView($values);
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
        return $this->_database()->updateView($view, $values);
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
        return $this->_database()->dropView($view);
    }

    /**
     * Get user defined types
     *
     * @param bool $withValues
     *
     * @return array<UserTypeDto>
     */
    public function userTypes(bool $withValues): array
    {
        return $this->_database()->userTypes($withValues);
    }

    /**
     * @param TableFieldDto $field
     *
     * @return array
     */
    public function enumValues(TableFieldDto $field): array
    {
        return $this->_database()->enumValues($field);
    }

    /**
     * Get existing schemas
     *
     * @return array
     */
    public function schemas(): array
    {
        return $this->_database()->schemas();
    }

    /**
     * Get events
     *
     * @return array
     */
    public function events(): array
    {
        return $this->_database()->events();
    }

    /**
     * Get information about stored routine
     *
     * @param string $name
     * @param string $type "FUNCTION" or "PROCEDURE"
     *
     * @return RoutineInfoDto|null
     */
    public function routine(string $name, string $type): RoutineInfoDto|null
    {
        return $this->_database()->routine($name, $type);
    }

    /**
     * Get list of routines
     *
     * @return array<RoutineDto>
     */
    public function routines(): array
    {
        return $this->_database()->routines();
    }

    /**
     * Get routine signature
     *
     * @param string $name
     * @param array $row result of routine()
     *
     * @return string
     */
    public function routineId(string $name, array $row): string
    {
        return $this->_database()->routineId($name, $row);
    }
}
