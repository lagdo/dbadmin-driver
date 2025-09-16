<?php

namespace Lagdo\DbAdmin\Driver\Driver\Db;

use Lagdo\DbAdmin\Driver\Db\Grammar;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Entity\TableSelectEntity;

trait GrammarTrait
{
    /**
     * @var Grammar
     */
    protected $grammar = null;

    /**
     * @inheritDoc
     */
    public function escapeId(string $idf): string
    {
        return $this->grammar->escapeId($idf);
    }

    /**
     * @inheritDoc
     */
    public function unescapeId(string $idf)
    {
        return $this->grammar->unescapeId($idf);
    }

    /**
     * @inheritDoc
     */
    public function convertField(TableFieldEntity $field)
    {
        return $this->grammar->convertField($field);
    }

    /**
     * @inheritDoc
     */
    public function unconvertField(TableFieldEntity $field, string $value)
    {
        return $this->grammar->unconvertField($field, $value);
    }

    /**
     * @inheritDoc
     */
    public function buildSelectQuery(TableSelectEntity $select)
    {
        return $this->grammar->buildSelectQuery($select);
    }

    /**
     * @inheritDoc
     */
    public function getAutoIncrementModifier()
    {
        return $this->grammar->getAutoIncrementModifier();
    }

    /**
     * @inheritDoc
     */
    public function getCreateTableQuery(string $table, bool $autoIncrement, string $style)
    {
        return $this->grammar->getCreateTableQuery($table, $autoIncrement, $style);
    }

    /**
     * @inheritDoc
     */
    public function getCreateIndexQuery(string $table, string $type, string $name, string $columns)
    {
        return $this->grammar->getCreateIndexQuery($table, $type, $name, $columns);
    }

    /**
     * @inheritDoc
     */
    public function getForeignKeysQuery(string $table)
    {
        return $this->grammar->getForeignKeysQuery($table);
    }

    /**
     * @inheritDoc
     */
    public function getTruncateTableQuery(string $table)
    {
        return $this->grammar->getTruncateTableQuery($table);
    }

    /**
     * @inheritDoc
     */
    public function getUseDatabaseQuery(string $database)
    {
        return $this->grammar->getUseDatabaseQuery($database);
    }

    /**
     * @inheritDoc
     */
    public function getCreateTriggerQuery(string $table)
    {
        return $this->grammar->getCreateTriggerQuery($table);
    }
}
