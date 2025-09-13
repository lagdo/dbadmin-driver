<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Lagdo\DbAdmin\Driver\Db\GrammarInterface;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Entity\TableSelectEntity;
use Lagdo\DbAdmin\Driver\Entity\ForeignKeyEntity;
use Lagdo\DbAdmin\Driver\Entity\QueryEntity;

trait GrammarTrait
{
    /**
     * @var GrammarInterface
     */
    protected $grammar;

    /**
     * Get escaped table name
     *
     * @param string $idf
     *
     * @return string
     */
    public function escapeTableName(string $idf)
    {
        return $this->grammar->escapeTableName($idf);
    }

    /**
     * Escape database identifier
     *
     * @param string $idf
     *
     * @return string
     */
    public function escapeId(string $idf)
    {
        return $this->grammar->escapeId($idf);
    }

    /**
     * Unescape database identifier
     *
     * @param string $idf
     *
     * @return string
     */
    public function unescapeId(string $idf)
    {
        return $this->grammar->unescapeId($idf);
    }

    /**
     * Convert field in select and edit
     *
     * @param TableFieldEntity $field one element from $this->fields()
     *
     * @return string
     */
    public function convertField(TableFieldEntity $field)
    {
        return $this->grammar->convertField($field);
    }

    /**
     * Convert value in edit after applying functions back
     *
     * @param TableFieldEntity $field One element from $this->fields()
     * @param string $value
     *
     * @return string
     */
    public function unconvertField(TableFieldEntity $field, string $value)
    {
        return $this->grammar->unconvertField($field, $value);
    }

    /**
     * Get select clause for convertible fields
     *
     * @param array $columns
     * @param array $fields
     * @param array $select
     *
     * @return string
     */
    public function convertFields(array $columns, array $fields, array $select = [])
    {
        return $this->grammar->convertFields($columns, $fields, $select);
    }

    /**
     * Select data from table
     *
     * @param TableSelectEntity $select
     *
     * @return string
     */
    public function buildSelectQuery(TableSelectEntity $select)
    {
        return $this->grammar->buildSelectQuery($select);
    }

    /**
     * Parse a string containing SQL queries
     *
     * @param QueryEntity $queryEntity
     *
     * @return bool
     */
    public function parseQueries(QueryEntity $queryEntity)
    {
        return $this->grammar->parseQueries($queryEntity);
    }

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
    public function getRowCountQuery(string $table, array $where, bool $isGroup, array $groups)
    {
        return $this->grammar->getRowCountQuery($table, $where, $isGroup, $groups);
    }

    /**
     * Get default value clause
     *
     * @param TableFieldEntity $field
     *
     * @return string
     */
    public function getDefaultValueClause(TableFieldEntity $field)
    {
        return $this->grammar->getDefaultValueClause($field);
    }

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
    public function getLimitClause(string $query, string $where, int $limit, int $offset = 0)
    {
        return $this->grammar->getLimitClause($query, $where, $limit, $offset);
    }

    /**
     * Format foreign key to use in SQL query
     *
     * @param ForeignKeyEntity $foreignKey
     *
     * @return string
     */
    public function formatForeignKey(ForeignKeyEntity $foreignKey)
    {
        return $this->grammar->formatForeignKey($foreignKey);
    }

    /**
     * Generate modifier for auto increment column
     *
     * @return string
     */
    public function getAutoIncrementModifier()
    {
        return $this->grammar->getAutoIncrementModifier();
    }

    /**
     * Get SQL command to create table
     *
     * @param string $table
     * @param bool $autoIncrement
     * @param string $style
     *
     * @return string
     */
    public function getCreateTableQuery(string $table, bool $autoIncrement, string $style)
    {
        return $this->grammar->getCreateTableQuery($table, $autoIncrement, $style);
    }

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
    public function getCreateIndexQuery(string $table, string $type, string $name, string $columns)
    {
        return $this->grammar->getCreateIndexQuery($table, $type, $name, $columns);
    }

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
    public function getForeignKeysQuery(string $table)
    {
        return $this->grammar->getForeignKeysQuery($table);
    }

    /**
     * Get SQL command to truncate table
     *
     * @param string $table
     *
     * @return string
     */
    public function getTruncateTableQuery(string $table)
    {
        return $this->grammar->getTruncateTableQuery($table);
    }

    /**
     * Get SQL command to change database
     *
     * @param string $database
     *
     * @return string
     */
    public function getUseDatabaseQuery(string $database)
    {
        return $this->grammar->getUseDatabaseQuery($database);
    }

    /**
     * Get SQL commands to create triggers
     *
     * @param string $table
     *
     * @return string
     */
    public function getCreateTriggerQuery(string $table)
    {
        return $this->grammar->getCreateTriggerQuery($table);
    }

    /**
     * Return query to get connection ID
     *
     * @return string
     */
    // public function connectionId()
    // {
    //     return $this->grammar->connectionId();
    // }
}
