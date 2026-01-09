<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Lagdo\DbAdmin\Driver\Dto\ColumnDto;
use Lagdo\DbAdmin\Driver\Dto\FieldType;
use Lagdo\DbAdmin\Driver\Dto\ForeignKeyDto;
use Lagdo\DbAdmin\Driver\Dto\QueryDto;
use Lagdo\DbAdmin\Driver\Dto\TableAlterDto;
use Lagdo\DbAdmin\Driver\Dto\TableCreateDto;
use Lagdo\DbAdmin\Driver\Dto\TableDto;
use Lagdo\DbAdmin\Driver\Dto\TableFieldDto;
use Lagdo\DbAdmin\Driver\Dto\TableSelectDto;

interface GrammarInterface
{
    /**
     * Escape database identifier
     *
     * @param string $idf
     *
     * @return string
     */
    public function escapeId(string $idf): string;

    /**
     * Unescape database identifier
     *
     * @param string $idf
     *
     * @return string
     */
    public function unescapeId(string $idf): string;

    /**
     * Convert field in select and edit
     *
     * @param TableFieldDto $field one element from $this->fields()
     *
     * @return string
     */
    public function convertField(TableFieldDto $field): string;

    /**
     * Convert value in edit after applying functions back
     *
     * @param TableFieldDto $field One element from $this->fields()
     * @param string $value
     *
     * @return string
     */
    public function unconvertField(TableFieldDto $field, string $value): string;

    /**
     * Select data from table
     *
     * @param TableSelectDto $select
     *
     * @return string
     */
    public function buildSelectQuery(TableSelectDto $select): string;

    /**
     * Build a query to select data from table
     *
     * @param string $table
     * @param array $select Result of processSelectColumns()[0]
     * @param array $where Result of processSelectWhere()
     * @param array $group Result of processSelectColumns()[1]
     * @param array $order Result of processSelectOrder()
     * @param int $limit Result of processSelectLimit()
     * @param int $page Index of page starting at zero
     *
     * @return string
     */
    public function getSelectQuery(string $table, array $select, array $where, array $group = [],
        array $order = [], int $limit = 1, int $page = 0): string;

    /**
     * Build a query to insert data into table
     *
     * @param string $table
     * @param array $values Escaped columns in keys, quoted data in values
     *
     * @return string
     */
    public function getInsertQuery(string $table, array $values): string;

    /**
     * Build a query to update data in table
     *
     * @param string $table
     * @param array $values Escaped columns in keys, quoted data in values
     * @param string $queryWhere " WHERE ..."
     * @param int $limit 0 or 1
     *
     * @return string
     */
    public function getUpdateQuery(string $table, array $values, string $queryWhere, int $limit = 0): string;

    /**
     * Build a query to delete data from table
     *
     * @param string $table
     * @param string $queryWhere " WHERE ..."
     * @param int $limit 0 or 1
     *
     * @return string
     */
    public function getDeleteQuery(string $table, string $queryWhere, int $limit = 0): string;

    /**
     * Generate modifier for auto increment column
     *
     * @return string
     */
    public function getAutoIncrementModifier(): string;

    /**
     * Get SQL commands to create a table
     *
     * @param TableCreateDto $table
     *
     * @return array<string>
     */
    public function getTableCreationQueries(TableCreateDto $table): array;

    /**
     * Get SQL commands to alter a table
     *
     * @param TableAlterDto $table
     *
     * @return array<string>
     */
    public function getTableAlterationQueries(TableAlterDto $table): array;

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
     * Command to create an index
     *
     * @param string $table
     * @param string $type
     * @param string $name
     * @param string $columns
     *
     * @return string
     */
    public function getIndexCreationQuery(string $table, string $type, string $name, string $columns): string;

    /**
     * Get SQL command to create foreign keys
     *
     * getTableDefinitionQueries() produces CREATE TABLE without FK CONSTRAINTs
     * getForeignKeysQueries() produces all FK CONSTRAINTs as ALTER TABLE ... ADD CONSTRAINT
     * so that all FKs can be added after all tables have been created, avoiding any need
     * to reorder CREATE TABLE statements in order of their FK dependencies
     *
     * @param TableDto $table
     *
     * @return array
     */
    public function getForeignKeysQueries(TableDto $table): array;

    /**
     * Get SQL command to truncate table
     *
     * @param string $table
     *
     * @return string
     */
    public function getTableTruncationQuery(string $table): string;

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
     * Get SQL commands to create triggers
     *
     * @param string $table
     *
     * @return string
     */
    public function getTriggerCreationQuery(string $table): string;

    /**
     * Get escaped table name
     *
     * @param string $idf
     *
     * @return string
     */
    public function escapeTableName(string $idf): string;

    /**
     * Get select clause for convertible fields
     *
     * @param array $columns
     * @param array $fields
     * @param array $select
     *
     * @return string
     */
    public function convertFields(array $columns, array $fields, array $select = []): string;

    /**
     * Parse a string containing SQL queries
     *
     * @param QueryDto $queryDto
     *
     * @return bool
     */
    public function parseQueries(QueryDto $queryDto): bool;

    /**
     * Get query to compute number of found rows
     *
     * @param string $table
     * @param array $where
     * @param bool $isGroup
     * @param array $groups
     *
     * @return string
     */
    public function getRowCountQuery(string $table, array $where, bool $isGroup, array $groups): string;

    /**
     * Remove current user definer from SQL command
     *
     * @param string $query
     *
     * @return string
     */
    public function removeDefiner(string $query): string;

    /**
     * Get default value clause
     *
     * @param TableFieldDto $field
     *
     * @return string
     */
    public function getDefaultValueClause(TableFieldDto $field): string;

    /**
     * Escape or unescape string to use inside form []
     *
     * @param string $idf
     * @param bool $back
     *
     * @return string
     */
    public function bracketEscape(string $idf, bool $back = false): string;

    /**
     * Escape column key used in where()
     *
     * @param string
     *
     * @return string
     */
    public function escapeKey(string $key): string;

    /**
     * Filter length value including enums
     *
     * @param string $length
     *
     * @return string
     */
    public function processLength(string $length): string;

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
}
