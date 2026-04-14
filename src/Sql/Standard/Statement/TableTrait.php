<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Statement;

use Lagdo\DbAdmin\Driver\Sql\DbProxyTrait;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\FieldType;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableFieldDto;

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
                ' DEFAULT ' . $this->_engine()->quote($field->default),
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
        $length = $this->_statement()->processLength($field->length);
        $type = preg_match($this->_engine()->numberRegex(), $field->type) &&
            in_array($field->unsigned, $this->_engine()->unsigned()) ?
            " {$field->unsigned}" : "";
        $collation = preg_match('~char|text|enum|set~', $field->type) && $field->collation ?
            " $collate " . ($this->_engine()->mssql() ? $field->collation :
                $this->_engine()->quote($field->collation)) : "";
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

        $column->name = $this->_statement()->escapeId($field->name);
        $column->type = $this->getFieldType($typeField);
        $column->nullValue = $field->nullable ? ' NULL' : ' NOT NULL'; // NULL for timestamp
        $column->defaultValue = $this->getDefaultValueClause($field);
        if (preg_match('~timestamp|datetime~', $field->type) && $field->onUpdate) {
            $column->onUpdate = " ON UPDATE {$field->onUpdate}";
        }
        if ($this->_engine()->support('comment') && $field->comment !== '') {
            $column->comment = ' COMMENT ' . $this->_engine()->quote($field->comment);
        }
        $column->autoIncrement = $field->autoIncrement ?
            $this->_statement()->getAutoIncrementModifier() : null;

        return $column;
    }
}
