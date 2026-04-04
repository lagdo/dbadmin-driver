<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Grammar;

interface DatabaseInterface
{
    /**
     * Check if utf8mb4 might be needed
     *
     * @param string $create
     *
     * @return void
     */
    public function setUtf8mb4(string $create): void;

    /**
     * Get SET NAMES query, if utf8mb4 might be needed
     *
     * @return string
     */
    public function getCharsetQuery(): string;

    /**
     * Get SQL command to change database
     *
     * @param string $database
     * @param string $style
     *
     * @return string
     */
    public function getUseDatabaseQuery(string $database, string $style = ''): string;

    /**
     * Create a database
     *
     * @param string $database
     * @param string $collation
     *
     * @return string
     */
    public function getCreateDatabaseQuery(string $database, string $collation): string;

    /**
     * Drop a database
     *
     * @param string $database
     *
     * @return string
     */
    public function getDropDatabaseQuery(string $database): string;

    /**
     * Generate modifier for auto increment column
     *
     * @return string
     */
    public function getAutoIncrementModifier(): string;

    /**
     * Command to drop views
     *
     * @param array $views
     *
     * @return array<string>
     */
    public function getDropViewsQueries(array $views): array;

    /**
     * Command to drop tables
     *
     * @param array $tables
     *
     * @return array<string>
     */
    public function getDropTablesQueries(array $tables): array;

    /**
     * Command to truncate tables
     *
     * @param array $tables
     *
     * @return array<string>
     */
    public function getTruncateTablesQueries(array $tables): array;
}
