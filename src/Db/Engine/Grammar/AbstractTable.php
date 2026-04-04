<?php

namespace Lagdo\DbAdmin\Support\Db\Engine\Grammar;

use Lagdo\DbAdmin\Support\DriverInterface;
use Lagdo\DbAdmin\Support\Db\Admin\Grammar\TableInterface;
use Lagdo\DbAdmin\Support\Dto\ColumnDto;
use Lagdo\DbAdmin\Support\Dto\FieldType;
use Lagdo\DbAdmin\Support\Dto\TableDto;
use Lagdo\DbAdmin\Support\Dto\TableFieldDto;
use Lagdo\DbAdmin\Support\GrammarInterface;
use Lagdo\DbAdmin\Support\Utils\Utils;

use function in_array;
use function preg_match;
use function str_ireplace;

abstract class AbstractTable implements TableInterface
{
    /**
     * @param DriverInterface $driver
     * @param GrammarInterface $grammar
     * @param Utils $utils
     */
    public function __construct(protected DriverInterface $driver,
        protected GrammarInterface $grammar, protected Utils $utils)
    {}

    /**
     * @inheritDoc
     */
    public function getForeignKeyQueries(TableDto $table): array
    {
        return [];
    }

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
     * Create SQL string from field type
     *
     * @param FieldType $field
     */
    public function getFieldType(FieldType $field, string $collate = "COLLATE"): string
    {
        $length = $this->grammar->processLength($field->length);
        $type = preg_match($this->driver->numberRegex(), $field->type) &&
            in_array($field->unsigned, $this->driver->unsigned()) ?
            " {$field->unsigned}" : "";
        $collation = preg_match('~char|text|enum|set~', $field->type) &&
            $field->collation ? " $collate " . ($this->driver->jush() === 'mssql' ?
                $field->collation : $this->driver->quote($field->collation)) : "";
        return " {$field->type}{$length}{$type}{$collation}";
    }

    /**
     * This is the process_field() function in Adminer.
     *
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

    /**
     * @inheritDoc
     */
    public function getCreateIndexQuery(string $table, string $type, string $name, string $columns): string
    {
        return '';
    }
}
