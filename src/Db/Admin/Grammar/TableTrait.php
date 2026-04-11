<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Grammar;

use Lagdo\DbAdmin\Support\Dto\ColumnDto;
use Lagdo\DbAdmin\Support\Dto\FieldType;
use Lagdo\DbAdmin\Support\Dto\TableFieldDto;

trait TableTrait
{
    /**
     * @var TableInterface
     */
    private TableInterface $table;

    /**
     * @return TableInterface
     */
    private function _t(): TableInterface
    {
        return $this->table ??= new Table($this->driver, $this, $this->utils);
    }

    /**
     * Get default value clause
     *
     * @param TableFieldDto $field
     *
     * @return string
     */
    public function getDefaultValueClause(TableFieldDto $field): string
    {
        return $this->_t()->getDefaultValueClause($field);
    }

    /**
     * Create SQL string from field type
     *
     * @param FieldType $field
     *
     * @return string
     */
    public function getFieldType(FieldType $field, string $collate = "COLLATE"): string
    {
        return $this->_t()->getFieldType($field, $collate);
    }

    /**
     * Create SQL string from field
     * This is the process_field() function in Adminer.
     *
     * @param TableFieldDto $field Basic field information
     * @param TableFieldDto $typeField Information about field type
     *
     * @return ColumnDto
     */
    public function getFieldClauses(TableFieldDto $field, TableFieldDto $typeField): ColumnDto
    {
        return $this->_t()->getFieldClauses($field, $typeField);
    }
}
