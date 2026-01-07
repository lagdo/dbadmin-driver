<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Lagdo\DbAdmin\Driver\Entity\ColumnEntity;
use Lagdo\DbAdmin\Driver\Entity\FieldType;
use Lagdo\DbAdmin\Driver\Entity\ForeignKeyEntity;
use Lagdo\DbAdmin\Driver\Entity\QueryEntity;
use Lagdo\DbAdmin\Driver\Entity\TableAlterEntity;
use Lagdo\DbAdmin\Driver\Entity\TableCreateEntity;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Entity\TableSelectEntity;

trait GrammarTrait
{
    /**
     * @var GrammarInterface
     */
    abstract protected function _grammar(): GrammarInterface;

    /**
     * @inheritDoc
     */
    public function escapeId(string $idf): string
    {
        return $this->_grammar()->escapeId($idf);
    }

    /**
     * @inheritDoc
     */
    public function unescapeId(string $idf): string
    {
        return $this->_grammar()->unescapeId($idf);
    }

    /**
     * @inheritDoc
     */
    public function convertField(TableFieldEntity $field): string
    {
        return $this->_grammar()->convertField($field);
    }

    /**
     * @inheritDoc
     */
    public function unconvertField(TableFieldEntity $field, string $value): string
    {
        return $this->_grammar()->unconvertField($field, $value);
    }

    /**
     * @inheritDoc
     */
    public function buildSelectQuery(TableSelectEntity $select): string
    {
        return $this->_grammar()->buildSelectQuery($select);
    }

    /**
     * @inheritDoc
     */
    public function getSelectQuery(string $table, array $select, array $where, array $group = [],
        array $order = [], int $limit = 1, int $page = 0): string
    {
        return $this->_grammar()->getSelectQuery($table, $select, $where, $group, $order, $limit, $page);
    }

    /**
     * @inheritDoc
     */
    public function getInsertQuery(string $table, array $values): string
    {
        return $this->_grammar()->getInsertQuery($table, $values);
    }

    /**
     * @inheritDoc
     */
    public function getUpdateQuery(string $table, array $values, string $queryWhere, int $limit = 0): string
    {
        return $this->_grammar()->getUpdateQuery($table, $values, $queryWhere, $limit);
    }

    /**
     * @inheritDoc
     */
    public function getDeleteQuery(string $table, string $queryWhere, int $limit = 0): string
    {
        return $this->_grammar()->getDeleteQuery($table, $queryWhere, $limit = 0);
    }

    /**
     * @inheritDoc
     */
    public function getAutoIncrementModifier(): string
    {
        return $this->_grammar()->getAutoIncrementModifier();
    }

    /**
     * Get SQL commands to create a table
     *
     * @param TableCreateEntity $table
     *
     * @return array<string>
     */
    public function getTableCreationQueries(TableCreateEntity $table): array
    {
        return $this->_grammar()->getTableCreationQueries($table);
    }

    /**
     * Get SQL commands to alter a table
     *
     * @param TableAlterEntity $table
     *
     * @return array<string>
     */
    public function getTableAlterationQueries(TableAlterEntity $table): array
    {
        return $this->_grammar()->getTableAlterationQueries($table);
    }

    /**
     * @inheritDoc
     */
    public function getTableDefinitionQueries(string $table, bool $autoIncrement, string $style): string
    {
        return $this->_grammar()->getTableDefinitionQueries($table, $autoIncrement, $style);
    }

    /**
     * @inheritDoc
     */
    public function getIndexCreationQuery(string $table, string $type, string $name, string $columns): string
    {
        return $this->_grammar()->getIndexCreationQuery($table, $type, $name, $columns);
    }

    /**
     * @inheritDoc
     */
    public function getForeignKeysQueries(TableEntity $table): array
    {
        return $this->_grammar()->getForeignKeysQueries($table);
    }

    /**
     * @inheritDoc
     */
    public function getTableTruncationQuery(string $table): string
    {
        return $this->_grammar()->getTableTruncationQuery($table);
    }

    /**
     * @inheritDoc
     */
    public function getUseDatabaseQuery(string $database, string $style = ''): string
    {
        return $this->_grammar()->getUseDatabaseQuery($database, $style);
    }

    /**
     * @inheritDoc
     */
    public function getTriggerCreationQuery(string $table): string
    {
        return $this->_grammar()->getTriggerCreationQuery($table);
    }

    /**
     * @inheritDoc
     */
    public function escapeTableName(string $idf): string
    {
        return $this->_grammar()->escapeTableName($idf);
    }

    /**
     * @inheritDoc
     */
    public function removeDefiner(string $query): string
    {
        return $this->_grammar()->removeDefiner($query);
    }

    /**
     * @inheritDoc
     */
    public function convertFields(array $columns, array $fields, array $select = []): string
    {
        return $this->_grammar()->convertFields($columns, $fields, $select);
    }

    /**
     * @inheritDoc
     */
    public function parseQueries(QueryEntity $queryEntity): bool
    {
        return $this->_grammar()->parseQueries($queryEntity);
    }

    /**
     * @inheritDoc
     */
    public function getRowCountQuery(string $table, array $where, bool $isGroup, array $groups): string
    {
        return $this->_grammar()->getRowCountQuery($table, $where, $isGroup, $groups);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultValueClause(TableFieldEntity $field): string
    {
        return $this->_grammar()->getDefaultValueClause($field);
    }

    /**
     * @inheritDoc
     */
    public function bracketEscape(string $idf, bool $back = false): string
    {
        return $this->_grammar()->bracketEscape($idf, $back);
    }

    /**
     * @inheritDoc
     */
    public function escapeKey(string $key): string
    {
        return $this->_grammar()->escapeKey($key);
    }

    /**
     * @inheritDoc
     */
    public function processLength(string $length): string
    {
        return $this->_grammar()->processLength($length);
    }

    /**
     * Create SQL string from field type
     *
     * @param FieldType $field
     */
    public function getFieldType(FieldType $field, string $collate = "COLLATE"): string
    {
        return $this->_grammar()->getFieldType($field, $collate);
    }

    /**
     * @inheritDoc
     */
    public function getFieldClauses(TableFieldEntity $field, TableFieldEntity $typeField): ColumnEntity
    {
        return $this->_grammar()->getFieldClauses($field, $typeField);
    }

    /**
     * @inheritDoc
     */
    public function setUtf8mb4(string $create): void
    {
        $this->_grammar()->setUtf8mb4($create);
    }

    /**
     * @inheritDoc
     */
    public function getCharsetQuery(): string
    {
        return $this->_grammar()->getCharsetQuery();
    }
}
