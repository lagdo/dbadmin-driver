<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Driver;

use Exception;

interface DatabaseInterface
{
    /**
     * Drop views
     *
     * @param array $views
     *
     * @return bool
     */
    public function dropViews(array $views): bool;

    /**
     * Drop tables
     *
     * @param array $tables
     *
     * @return bool
     */
    public function dropTables(array $tables): bool;

    /**
     * Truncate tables
     *
     * @param array $tables
     *
     * @return bool
     */
    public function truncateTables(array $tables): bool;

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
}
