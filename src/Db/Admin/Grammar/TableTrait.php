<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Grammar;

use Lagdo\DbAdmin\Support\Dto\ColumnDto;
use Lagdo\DbAdmin\Support\Dto\FieldType;
use Lagdo\DbAdmin\Support\Dto\TableAlterDto;
use Lagdo\DbAdmin\Support\Dto\TableCreateDto;
use Lagdo\DbAdmin\Support\Dto\TableDto;
use Lagdo\DbAdmin\Support\Dto\TableFieldDto;

trait TableTrait
{
    /**
     * @return TableInterface
     */
    abstract protected function _table(): TableInterface;

    /**
     * Get SQL commands to create a table
     *
     * @param TableCreateDto $table
     *
     * @return array<string>
     */
    public function getCreateTableQueries(TableCreateDto $table): array
    {
        return $this->_table()->getCreateTableQueries($table);
    }

    /**
     * Get SQL commands to alter a table
     *
     * @param TableAlterDto $table
     *
     * @return array<string>
     */
    public function getAlterTableQueries(TableAlterDto $table): array
    {
        return $this->_table()->getAlterTableQueries($table);
    }

    /**
     * @inheritDoc
     */
    public function getTableDefinitionQueries(string $table, bool $autoIncrement, string $style): string
    {
        return $this->_table()->getTableDefinitionQueries($table, $autoIncrement, $style);
    }

    /**
     * @inheritDoc
     */
    public function getForeignKeyQueries(TableDto $table): array
    {
        return $this->_table()->getForeignKeyQueries($table);
    }

    /**
     * @inheritDoc
     */
    public function getDefaultValueClause(TableFieldDto $field): string
    {
        return $this->_table()->getDefaultValueClause($field);
    }

    /**
     * Create SQL string from field type
     *
     * @param FieldType $field
     */
    public function getFieldType(FieldType $field, string $collate = "COLLATE"): string
    {
        return $this->_table()->getFieldType($field, $collate);
    }

    /**
     * @inheritDoc
     */
    public function getFieldClauses(TableFieldDto $field, TableFieldDto $typeField): ColumnDto
    {
        return $this->_table()->getFieldClauses($field, $typeField);
    }

    /**
     * @inheritDoc
     */
    public function getCreateIndexQuery(string $table, string $type, string $name, string $columns): string
    {
        return $this->_table()->getCreateIndexQuery($table, $type, $name, $columns);
    }

    /**
     * @inheritDoc
     */
    public function getAlterIndexQueries(string $table, array $alter, array $drop): array
    {
        return $this->_table()->getAlterIndexQueries($table, $alter, $drop);
    }

    /**
     * @inheritDoc
     */
    public function getTruncateTableQuery(string $table): string
    {
        return $this->_table()->getTruncateTableQuery($table);
    }

    /**
     * @inheritDoc
     */
    public function getCreateTriggerQuery(string $table): string
    {
        return $this->_table()->getCreateTriggerQuery($table);
    }
}
