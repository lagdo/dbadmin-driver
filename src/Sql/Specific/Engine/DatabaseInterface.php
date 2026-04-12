<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Engine;

use Lagdo\DbAdmin\Driver\Sql\Dto\RoutineDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\RoutineInfoDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableFieldDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\UserTypeDto;

interface DatabaseInterface
{
    /**
     * Get cached list of databases
     *
     * @param bool $flush
     *
     * @return array
     */
    public function databases(bool $flush): array;

    /**
     * Compute size of database
     *
     * @param string $database
     *
     * @return int
     */
    public function databaseSize(string $database): int;

    /**
     * Get database collation
     *
     * @param string $database
     * @param array $collations
     *
     * @return string
     */
    public function databaseCollation(string $database, array $collations): string;

    /**
     * Find out if database is information_schema
     *
     * @param string $database
     *
     * @return bool
     */
    public function isInformationSchema(string $database): bool;

    /**
     * Find out if database is a system database
     *
     * @param string $database
     *
     * @return bool
     */
    public function isSystemSchema(string $database): bool;

    /**
     * Create a database
     *
     * @param string $database
     * @param string $collation
     *
     * @return boolean
     */
    public function createDatabase(string $database, string $collation): bool;

    /**
     * Drop a database
     *
     * @param string $database
     *
     * @return bool
     */
    public function dropDatabase(string $database): bool;

    /**
     * Get tables list
     *
     * @return array
     */
    public function tables(): array;

    /**
     * Get sequences list
     *
     * @return array
     */
    public function sequences(): array;

    /**
     * Count tables in all databases
     *
     * @param array $databases
     *
     * @return array
     */
    public function countTables(array $databases): array;

    /**
     * Get user defined types
     *
     * @param bool $withValues
     *
     * @return array<UserTypeDto>
     */
    public function userTypes(bool $withValues): array;

    /**
     * @param TableFieldDto $field
     *
     * @return array
     */
    public function enumValues(TableFieldDto $field): array;

    /**
     * Get existing schemas
     *
     * @return array
     */
    public function schemas(): array;

    /**
     * Get events
     *
     * @return array
     */
    public function events(): array;

    /**
     * Get information about stored routine
     *
     * @param string $name
     * @param string $type "FUNCTION" or "PROCEDURE"
     *
     * @return RoutineInfoDto|null
     */
    public function routine(string $name, string $type): RoutineInfoDto|null;

    /**
     * Get list of routines
     *
     * @return array<RoutineDto>
     */
    public function routines(): array;

    /**
     * Get routine signature
     *
     * @param string $name
     * @param array $row result of routine()
     *
     * @return string
     */
    public function routineId(string $name, array $row): string;
}
