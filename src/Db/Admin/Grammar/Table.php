<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Grammar;

use Lagdo\DbAdmin\Support\Db\AbstractDelegate;
use Lagdo\DbAdmin\Support\Dto\ColumnDto;
use Lagdo\DbAdmin\Support\Dto\FieldType;
use Lagdo\DbAdmin\Support\Dto\TableFieldDto;

use function in_array;
use function preg_match;
use function str_ireplace;

class Table extends AbstractDelegate implements TableInterface
{
    /**
     * @inheritDoc
     */
    public function getDefaultValueClause(TableFieldDto $field): string
    {
        return match(true) {
            $field->default === null => '',
            preg_match('~char|binary|text|enum|set~', $field->type) > 0,
            preg_match('~^(?![a-z])~i', $field->default) > 0 =>
                ' DEFAULT ' . $this->driver->quote($field->default),
            default => " DEFAULT {$field->default}",
        };
    }

    /**
     * @inheritDoc
     */
    public function getFieldType(FieldType $field, string $collate = "COLLATE"): string
    {
        $length = $this->grammar->processLength($field->length);
        $type = preg_match($this->driver->numberRegex(), $field->type) &&
            in_array($field->unsigned, $this->driver->unsigned()) ?
            " {$field->unsigned}" : "";
        $collation = preg_match('~char|text|enum|set~', $field->type) &&
            $field->collation ? " $collate " . ($this->driver->mssql() ?
                $field->collation : $this->driver->quote($field->collation)) : "";
        return " {$field->type}{$length}{$type}{$collation}";
    }

    /**
     * @inheritDoc
     */
    public function getFieldClauses(TableFieldDto $field, TableFieldDto $typeField): ColumnDto
    {
        // MariaDB exports CURRENT_TIMESTAMP as a function.
        if ($field->onUpdate) {
            $field->onUpdate = str_ireplace("current_timestamp()", "CURRENT_TIMESTAMP", $field->onUpdate);
        }

        $column = new ColumnDto($field);

        $column->name = $this->grammar->escapeId($field->name);
        $column->type = $this->getFieldType($typeField);
        $column->nullValue = $field->nullable ? ' NULL' : ' NOT NULL'; // NULL for timestamp
        $column->defaultValue = $this->getDefaultValueClause($field);
        if (preg_match('~timestamp|datetime~', $field->type) && $field->onUpdate) {
            $column->onUpdate = " ON UPDATE {$field->onUpdate}";
        }
        if ($this->driver->support('comment') && $field->comment !== '') {
            $column->comment = ' COMMENT ' . $this->driver->quote($field->comment);
        }
        $column->autoIncrement = $field->autoIncrement ? $this->grammar->getAutoIncrementModifier() : null;

        return $column;
    }
}
