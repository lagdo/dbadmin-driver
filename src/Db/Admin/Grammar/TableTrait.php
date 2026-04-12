<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Grammar;

use Lagdo\DbAdmin\Support\Db\DbProxyTrait;
use Lagdo\DbAdmin\Support\Dto\ColumnDto;
use Lagdo\DbAdmin\Support\Dto\FieldType;
use Lagdo\DbAdmin\Support\Dto\TableFieldDto;

use function in_array;
use function preg_match;
use function str_ireplace;

trait TableTrait
{
    use DbProxyTrait;

    /**
     * Get default value clause
     *
     * @param TableFieldDto $field
     *
     * @return string
     */
    public function getDefaultValueClause(TableFieldDto $field): string
    {
        return match(true) {
            $field->default === null => '',
            preg_match('~char|binary|text|enum|set~', $field->type) > 0,
            preg_match('~^(?![a-z])~i', $field->default) > 0 =>
                ' DEFAULT ' . $this->_driver()->quote($field->default),
            default => " DEFAULT {$field->default}",
        };
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
        $length = $this->_grammar()->processLength($field->length);
        $type = preg_match($this->_driver()->numberRegex(), $field->type) &&
            in_array($field->unsigned, $this->_driver()->unsigned()) ?
            " {$field->unsigned}" : "";
        $collation = preg_match('~char|text|enum|set~', $field->type) &&
            $field->collation ? " $collate " . ($this->_driver()->mssql() ?
                $field->collation : $this->_driver()->quote($field->collation)) : "";
        return " {$field->type}{$length}{$type}{$collation}";
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
        // MariaDB exports CURRENT_TIMESTAMP as a function.
        if ($field->onUpdate) {
            $field->onUpdate = str_ireplace("current_timestamp()", "CURRENT_TIMESTAMP", $field->onUpdate);
        }

        $column = new ColumnDto($field);

        $column->name = $this->_grammar()->escapeId($field->name);
        $column->type = $this->getFieldType($typeField);
        $column->nullValue = $field->nullable ? ' NULL' : ' NOT NULL'; // NULL for timestamp
        $column->defaultValue = $this->getDefaultValueClause($field);
        if (preg_match('~timestamp|datetime~', $field->type) && $field->onUpdate) {
            $column->onUpdate = " ON UPDATE {$field->onUpdate}";
        }
        if ($this->_driver()->support('comment') && $field->comment !== '') {
            $column->comment = ' COMMENT ' . $this->_driver()->quote($field->comment);
        }
        $column->autoIncrement = $field->autoIncrement ? $this->_grammar()->getAutoIncrementModifier() : null;

        return $column;
    }
}
