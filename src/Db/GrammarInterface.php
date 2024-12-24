<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Entity\TableSelectEntity;
use Lagdo\DbAdmin\Driver\Entity\ForeignKeyEntity;
use Lagdo\DbAdmin\Driver\Entity\QueryEntity;

interface GrammarInterface
{
    /**
     * Get escaped table name
     *
     * @param string $idf
     *
     * @return string
     */
    public function escapeTableName(string $idf);

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
     * Select data from table
     *
     * @param TableSelectEntity $select
     *
     * @return string
     */
    public function buildSelectQuery(TableSelectEntity $select);

    /**
     * Parse a string containing SQL queries
     *
     * @param QueryEntity $queryEntity
     *
     * @return bool
     */
    public function parseQueries(QueryEntity $queryEntity);

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
    public function getRowCountQuery(string $table, array $where, bool $isGroup, array $groups);

    /**
     * Get default value clause
     *
     * @param TableFieldEntity $field
     *
     * @return string
     */
    public function getDefaultValueClause(TableFieldEntity $field);

    /**
     * Formulate SQL query with limit
     *
     * @param string $query Everything after SELECT
     * @param string $where Including WHERE
     * @param int $limit
     * @param int $offset
     *
     * @return string
     */
    public function getLimitClause(string $query, string $where, int $limit, int $offset = 0);

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
    public function getAutoIncrementModifier();

    /**
     * Get SQL command to create table
     *
     * @param string $table
     * @param bool $autoIncrement
     * @param string $style
     *
     * @return string
     */
    public function getCreateTableQuery(string $table, bool $autoIncrement, string $style);

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
    public function getCreateIndexQuery(string $table, string $type, string $name, string $columns);

    /**
     * Get SQL command to create foreign keys
     *
     * getCreateTableQuery() produces CREATE TABLE without FK CONSTRAINTs
     * getForeignKeysQuery() produces all FK CONSTRAINTs as ALTER TABLE ... ADD CONSTRAINT
     * so that all FKs can be added after all tables have been created, avoiding any need
     * to reorder CREATE TABLE statements in order of their FK dependencies
     *
     * @param string $table
     *
     * @return string
     */
    public function getForeignKeysQuery(string $table);

    /**
     * Get SQL command to truncate table
     *
     * @param string $table
     *
     * @return string
     */
    public function getTruncateTableQuery(string $table);

    /**
     * Get SQL command to change database
     *
     * @param string $database
     *
     * @return string
     */
    public function getUseDatabaseQuery(string $database);

    /**
     * Get SQL commands to create triggers
     *
     * @param string $table
     *
     * @return string
     */
    public function getCreateTriggerQuery(string $table);

    /**
     * Return query to get connection ID
     *
     * @return string
     */
    // public function connectionId();
}
