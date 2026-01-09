<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Exception;
use Lagdo\DbAdmin\Driver\Dto\RoutineDto;
use Lagdo\DbAdmin\Driver\Dto\RoutineInfoDto;
use Lagdo\DbAdmin\Driver\Dto\TableDto;
use Lagdo\DbAdmin\Driver\Dto\TableFieldDto;
use Lagdo\DbAdmin\Driver\Dto\UserTypeDto;

interface DatabaseInterface
{
    /**
     * Alter indexes
     *
     * @param string $table Escaped table name
     * @param array $alter  Indexes to alter. Array of IndexDto.
     * @param array $drop   Indexes to drop. Array of IndexDto.
     *
     * @return bool
     */
    public function alterIndexes(string $table, array $alter, array $drop): bool;

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
     * Drop views
     *
     * @param array $views
     *
     * @return bool
     */
    public function dropViews(array $views): bool;

    /**
     * Truncate tables
     *
     * @param array $tables
     *
     * @return bool
     */
    public function truncateTables(array $tables): bool;

    /**
     * Drop tables
     *
     * @param array $tables
     *
     * @return bool
     */
    public function dropTables(array $tables): bool;

    /**
     * Move tables to other schema
     *
     * @param array $tables
     * @param array $views
     * @param string $target
     *
     * @return bool
     */
    public function moveTables(array $tables, array $views, string $target): bool;

    /**
     * Copy tables to other schema
     *
     * @param array $tables
     * @param array $views
     * @param string $target
     *
     * @return bool
     */
    public function copyTables(array $tables, array $views, string $target): bool;

    /**
     * Create a view
     *
     * @param array $values The view values
     *
     * @return bool
     * @throws Exception
     */
    public function createView(array $values): bool;

    /**
     * Update a view
     *
     * @param string $view The view name
     * @param array $values The view values
     *
     * @return string
     * @throws Exception
     */
    public function updateView(string $view, array $values): string;

    /**
     * Drop a view
     *
     * @param string $view The view name
     *
     * @return bool
     * @throws Exception
     */
    public function dropView(string $view): bool;

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
