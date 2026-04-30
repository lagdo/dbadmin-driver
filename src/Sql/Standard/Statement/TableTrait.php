<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Statement;

use Lagdo\DbAdmin\Driver\Sql\DbProxyTrait;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnInputDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnType;

use function in_array;
use function preg_match;
use function str_ireplace;

trait TableTrait
{
    use DbProxyTrait;

    /**
     * Get default value clause
     *
     * @param ColumnDto $column
     *
     * @return string
     */
    public function getDefaultValueClause(ColumnDto $column): string
    {
        return match(true) {
            $column->default === null => '',
            preg_match('~char|binary|text|enum|set~', $column->type) > 0,
            preg_match('~^(?![a-z])~i', $column->default) > 0 =>
                ' DEFAULT ' . $this->_engine()->quote($column->default),
            default => " DEFAULT {$column->default}",
        };
    }

    /**
     * Create SQL string from column type
     *
     * @param ColumnType $column
     *
     * @return string
     */
    public function getColumnType(ColumnType $column, string $collate = "COLLATE"): string
    {
        $length = $this->_statement()->processLength($column->length);
        $type = preg_match($this->_engine()->numberRegex(), $column->type) &&
            in_array($column->unsigned, $this->_engine()->unsigned()) ?
            " {$column->unsigned}" : "";
        $collation = preg_match('~char|text|enum|set~', $column->type) && $column->collation ?
            " $collate " . ($this->_engine()->mssql() ? $column->collation :
                $this->_engine()->quote($column->collation)) : "";
        return " {$column->type}{$length}{$type}{$collation}";
    }

    /**
     * Create SQL string from column
     * This is the process_field() function in Adminer.
     *
     * @param ColumnDto $column Basic column information
     * @param ColumnDto $typeColumn Information about column type
     *
     * @return ColumnInputDto
     */
    public function makeColumnInput(ColumnDto $column, ColumnDto $typeColumn): ColumnInputDto
    {
        // MariaDB exports CURRENT_TIMESTAMP as a function.
        if ($column->onUpdate) {
            $column->onUpdate = str_ireplace("current_timestamp()", "CURRENT_TIMESTAMP", $column->onUpdate);
        }

        $input = new ColumnInputDto($column);

        $input->name = $this->_statement()->escapeId($column->name);
        $input->type = $this->getColumnType($typeColumn);
        $input->nullValue = $column->nullable ? ' NULL' : ' NOT NULL'; // NULL for timestamp
        $input->defaultValue = $this->getDefaultValueClause($column);
        if (preg_match('~timestamp|datetime~', $column->type) && $column->onUpdate) {
            $input->onUpdate = " ON UPDATE {$column->onUpdate}";
        }
        if ($this->_engine()->support('comment') && $column->comment !== null) {
            $input->comment = ' COMMENT ' . $this->_engine()->quote($column->comment);
        }
        $input->autoIncrement = $column->autoIncrement ?
            $this->_statement()->getAutoIncrementModifier() : null;

        return $input;
    }
}
