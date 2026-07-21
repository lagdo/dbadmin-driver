<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Statement;

use Lagdo\DbAdmin\Driver\Sql\Dto\IndexDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableDto;

interface DatabaseInterface
{
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

    /**
     * Get SQL command to create table
     *
     * @param string $table
     * @param bool $autoIncrement
     * @param string $style
     *
     * @return string
     */
    public function getExportTableQueries(string $table, bool $autoIncrement, string $style): string;

    /**
     * Get SQL command to create foreign keys
     *
     * getExportTableQueries() produces CREATE TABLE without FK CONSTRAINTs
     * getForeignKeyQueries() produces all FK CONSTRAINTs as ALTER TABLE ... ADD CONSTRAINT
     * so that all FKs can be added after all tables have been created, avoiding any need
     * to reorder CREATE TABLE statements in order of their FK dependencies
     *
     * @param TableDto $table
     *
     * @return array
     */
    public function getForeignKeyQueries(TableDto $table): array;

    /**
     * Command to create an index
     *
     * @param string $table
     * @param string $type
     * @param string $name
     * @param string $columns
     *
     * @return string
     */
    public function getCreateIndexQuery(string $table, string $type, string $name, string $columns): string;

    /**
     * Command to alter indexes
     *
     * @param string $table Escaped table name
     * @param array<IndexDto> $alter  Indexes to alter
     * @param array<IndexDto> $drop   Indexes to drop
     *
     * @return array<string>
     */
    public function getAlterIndexQueries(string $table, array $alter, array $drop): array;

    /**
     * Get SQL command to truncate table
     *
     * @param string $table
     *
     * @return string
     */
    public function getTruncateTableQuery(string $table): string;

    /**
     * Get SQL commands to create triggers
     *
     * @param string $table
     *
     * @return string
     */
    public function getCreateTriggerQuery(string $table): string;
}
