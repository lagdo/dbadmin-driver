<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Entity\TableSelectEntity;
use Lagdo\DbAdmin\Driver\Entity\ForeignKeyEntity;
use Lagdo\DbAdmin\Driver\Entity\QueryEntity;

trait GrammarTrait
{
    /**
     * Get escaped table name
     *
     * @param string $idf
     *
     * @return string
     */
    public function table(string $idf)
    {
        return $this->grammar->table($idf);
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
    public function sqlForRowCount(string $table, array $where, bool $isGroup, array $groups)
    {
        return $this->grammar->sqlForRowCount($table, $where, $isGroup, $groups);
    }

    /**
     * Get default value clause
     *
     * @param TableFieldEntity $field
     *
     * @return string
     */
    public function defaultValue(TableFieldEntity $field)
    {
        return $this->grammar->defaultValue($field);
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
    public function limit(string $query, string $where, int $limit, int $offset = 0)
    {
        return $this->grammar->limit($query, $where, $limit, $offset);
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
    public function autoIncrement()
    {
        return $this->grammar->autoIncrement();
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
    public function sqlForCreateTable(string $table, bool $autoIncrement, string $style)
    {
        return $this->grammar->sqlForCreateTable($table, $autoIncrement, $style);
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
    public function sqlForCreateIndex(string $table, string $type, string $name, string $columns)
    {
        return $this->grammar->sqlForCreateIndex($table, $type, $name, $columns);
    }

    /**
     * Get SQL command to create foreign keys
     *
     * sqlForCreateTable() produces CREATE TABLE without FK CONSTRAINTs
     * sqlForForeignKeys() produces all FK CONSTRAINTs as ALTER TABLE ... ADD CONSTRAINT
     * so that all FKs can be added after all tables have been created, avoiding any need
     * to reorder CREATE TABLE statements in order of their FK dependencies
     *
     * @param string $table
     *
     * @return string
     */
    public function sqlForForeignKeys(string $table)
    {
        return $this->grammar->sqlForForeignKeys($table);
    }

    /**
     * Get SQL command to truncate table
     *
     * @param string $table
     *
     * @return string
     */
    public function sqlForTruncateTable(string $table)
    {
        return $this->grammar->sqlForTruncateTable($table);
    }

    /**
     * Get SQL command to change database
     *
     * @param string $database
     *
     * @return string
     */
    public function sqlForUseDatabase(string $database)
    {
        return $this->grammar->sqlForUseDatabase($database);
    }

    /**
     * Get SQL commands to create triggers
     *
     * @param string $table
     *
     * @return string
     */
    public function sqlForCreateTrigger(string $table)
    {
        return $this->grammar->sqlForCreateTrigger($table);
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
