<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\Entity\IndexEntity;
use Lagdo\DbAdmin\Driver\Entity\ForeignKeyEntity;
use Lagdo\DbAdmin\Driver\Entity\TriggerEntity;
use Lagdo\DbAdmin\Driver\Entity\RoutineEntity;

interface GrammarInterface
{
    /**
     * Get escaped table name
     *
     * @param string $idf
     *
     * @return string
     */
    public function table(string $idf);

    /**
     * Escape database identifier
     *
     * @param string $idf
     *
     * @return string
     */
    public function escapeId(string $idf);

    /**
     * Unescape database identifier
     *
     * @param string $idf
     *
     * @return string
     */
    public function unescapeId(string $idf);

    /**
     * Convert field in select and edit
     *
     * @param TableFieldEntity $field one element from $this->fields()
     *
     * @return string
     */
    public function convertField(TableFieldEntity $field);

    /**
     * Convert value in edit after applying functions back
     *
     * @param TableFieldEntity $field One element from $this->fields()
     * @param string $value
     *
     * @return string
     */
    public function unconvertField(TableFieldEntity $field, string $value);

    /**
     * Get select clause for convertible fields
     *
     * @param array $columns
     * @param array $fields
     * @param array $select
     *
     * @return string
     */
    public function convertFields(array $columns, array $fields, array $select = []);

    /**
     * Shortcut for $this->connection->quote($string)
     *
     * @param string $string
     *
     * @return string
     */
    public function quote(string $string);

    /**
     * Select data from table
     *
     * @param array $select
     * @param array $where
     * @param array $group
     * @param array $order
     * @param int $limit
     * @param int $page
     *
     * @return string
     */
    public function buildSelectQuery(array $select, array $where, array $group, array $order = [], int $limit = 1, int $page = 0);

    /**
     * Apply SQL function
     *
     * @param string $function
     * @param string $column escaped column identifier
     *
     * @return string
     */
    public function applySqlFunction(string $function, string $column);

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
    public function countRowsSql(string $table, array $where, bool $isGroup, array $groups);

    /**
     * Get default value clause
     *
     * @param TableFieldEntity $field
     *
     * @return string
     */
    public function defaultValue(TableFieldEntity $field);

    /**
     * Formulate SQL query with limit
     *
     * @param string $query Everything after SELECT
     * @param string $where Including WHERE
     * @param int $limit
     * @param int $offset
     * @param string $separator
     *
     * @return string
     */
    public function limit(string $query, string $where, int $limit, int $offset = 0, string $separator = " ");

    /**
     * Formulate SQL modification query with limit 1
     *
     * @param string $table
     * @param string $query Everything after UPDATE or DELETE
     * @param string $where
     * @param string $separator
     *
     * @return string
     */
    public function limitToOne(string $table, string $query, string $where, string $separator = "\n");

    /**
     * Format foreign key to use in SQL query
     *
     * @param ForeignKeyEntity $foreignKey
     *
     * @return string
     */
    public function formatForeignKey(ForeignKeyEntity $foreignKey);

    /**
     * Generate modifier for auto increment column
     *
     * @return string
     */
    public function autoIncrement();

    /**
     * Get SQL command to create table
     *
     * @param string $table
     * @param bool $autoIncrement
     * @param string $style
     *
     * @return string
     */
    public function createTableSql(string $table, bool $autoIncrement, string $style);

    /**
     * Command to create an index
     *
     * @param string $table
     * @param string $type
     * @param string $name
     * @param array $columns
     *
     * @return string
     */
    public function createIndexSql(string $table, string $type, string $name, array $columns);

    /**
     * Get SQL command to create foreign keys
     *
     * createTableSql() produces CREATE TABLE without FK CONSTRAINTs
     * foreignKeysSql() produces all FK CONSTRAINTs as ALTER TABLE ... ADD CONSTRAINT
     * so that all FKs can be added after all tables have been created, avoiding any need
     * to reorder CREATE TABLE statements in order of their FK dependencies
     *
     * @param string $table
     *
     * @return string
     */
    public function foreignKeysSql(string $table);

    /**
     * Get SQL command to truncate table
     *
     * @param string $table
     *
     * @return string
     */
    public function truncateTableSql(string $table);

    /**
     * Get SQL command to change database
     *
     * @param string $database
     *
     * @return string
     */
    public function useDatabaseSql(string $database);

    /**
     * Get SQL commands to create triggers
     *
     * @param string $table
     *
     * @return string
     */
    public function createTriggerSql(string $table);

    /**
     * Return query to get connection ID
     *
     * @return string
     */
    // public function connectionId();
}
