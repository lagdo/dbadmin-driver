<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Grammar;

use Lagdo\DbAdmin\Support\Dto\ColumnDto;
use Lagdo\DbAdmin\Support\Dto\FieldType;
use Lagdo\DbAdmin\Support\Dto\TableAlterDto;
use Lagdo\DbAdmin\Support\Dto\TableCreateDto;
use Lagdo\DbAdmin\Support\Dto\TableDto;
use Lagdo\DbAdmin\Support\Dto\TableFieldDto;

interface TableInterface
{
    /**
     * Get SQL commands to create a table
     *
     * @param TableCreateDto $table
     *
     * @return array<string>
     */
    public function getCreateTableQueries(TableCreateDto $table): array;

    /**
     * Get SQL commands to alter a table
     *
     * @param TableAlterDto $table
     *
     * @return array<string>
     */
    public function getAlterTableQueries(TableAlterDto $table): array;

    /**
     * Get SQL command to create table
     *
     * @param string $table
     * @param bool $autoIncrement
     * @param string $style
     *
     * @return string
     */
    public function getTableDefinitionQueries(string $table, bool $autoIncrement, string $style): string;

    /**
     * Get SQL command to create foreign keys
     *
     * getTableDefinitionQueries() produces CREATE TABLE without FK CONSTRAINTs
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
     * Get default value clause
     *
     * @param TableFieldDto $field
     *
     * @return string
     */
    public function getDefaultValueClause(TableFieldDto $field): string;

    /**
     * Create SQL string from field type
     *
     * @param FieldType $field
     *
     * @return string
     */
    public function getFieldType(FieldType $field, string $collate = "COLLATE"): string;

    /**
     * Create SQL string from field
     *
     * @param TableFieldDto $field Basic field information
     * @param TableFieldDto $typeField Information about field type
     *
     * @return ColumnDto
     */
    public function getFieldClauses(TableFieldDto $field, TableFieldDto $typeField): ColumnDto;

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
     * @param array $alter<IndexDto>  Indexes to alter
     * @param array $drop<IndexDto>   Indexes to drop
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
