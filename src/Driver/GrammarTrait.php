<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Lagdo\DbAdmin\Driver\Entity\FieldType;
use Lagdo\DbAdmin\Driver\Entity\ForeignKeyEntity;
use Lagdo\DbAdmin\Driver\Entity\QueryEntity;
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
    public function getAutoIncrementModifier(): string
    {
        return $this->_grammar()->getAutoIncrementModifier();
    }

    /**
     * @inheritDoc
     */
    public function getCreateTableQuery(string $table, bool $autoIncrement, string $style): string
    {
        return $this->_grammar()->getCreateTableQuery($table, $autoIncrement, $style);
    }

    /**
     * @inheritDoc
     */
    public function getCreateIndexQuery(string $table, string $type, string $name, string $columns): string
    {
        return $this->_grammar()->getCreateIndexQuery($table, $type, $name, $columns);
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
    public function getTruncateTableQuery(string $table): string
    {
        return $this->_grammar()->getTruncateTableQuery($table);
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
    public function getCreateTriggerQuery(string $table): string
    {
        return $this->_grammar()->getCreateTriggerQuery($table);
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
    public function getLimitClause(string $query, string $where, int $limit, int $offset = 0): string
    {
        return $this->_grammar()->getLimitClause($query, $where, $limit, $offset);
    }

    /**
     * @inheritDoc
     */
    public function formatForeignKey(ForeignKeyEntity $foreignKey): string
    {
        return $this->_grammar()->formatForeignKey($foreignKey);
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
    public function processType(FieldType $field, string $collate = "COLLATE"): string
    {
        return $this->_grammar()->processType($field, $collate);
    }

    /**
     * @inheritDoc
     */
    public function processField(TableFieldEntity $field, TableFieldEntity $typeField): array
    {
        return $this->_grammar()->processField($field, $typeField);
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
