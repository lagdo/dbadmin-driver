<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Engine;

use Lagdo\DbAdmin\Driver\Sql\Dto\RoutineDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\RoutineInfoDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableFieldDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\UserTypeDto;

trait DatabaseTrait
{
    /**
     * @return AbstractDatabase
     */
    abstract protected function _database(): AbstractDatabase;

    /**
     * Get cached list of databases
     *
     * @param bool $flush
     *
     * @return array
     */
    public function databases(bool $flush): array
    {
        return $this->_database()->databases($flush);
    }

    /**
     * Compute size of database
     *
     * @param string $database
     *
     * @return int
     */
    public function databaseSize(string $database): int
    {
        return $this->_database()->databaseSize($database);
    }

    /**
     * Get database collation
     *
     * @param string $database
     * @param array $collations
     *
     * @return string
     */
    public function databaseCollation(string $database, array $collations): string
    {
        return $this->_database()->databaseCollation($database, $collations);
    }

    /**
     * Find out if database is information_schema
     *
     * @param string $database
     *
     * @return bool
     */
    public function isInformationSchema(string $database): bool
    {
        return $this->_database()->isInformationSchema($database);
    }

    /**
     * Find out if database is a system database
     *
     * @param string $database
     *
     * @return bool
     */
    public function isSystemSchema(string $database): bool
    {
        return $this->_database()->isSystemSchema($database);
    }

    /**
     * Create a database
     *
     * @param string $database
     * @param string $collation
     *
     * @return string|boolean
     */
    public function createDatabase(string $database, string $collation): bool
    {
        return $this->_database()->createDatabase($database, $collation);
    }

    /**
     * Drop a database
     *
     * @param string $database
     *
     * @return bool
     */
    public function dropDatabase(string $database): bool
    {
        return $this->_database()->dropDatabase($database);
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
