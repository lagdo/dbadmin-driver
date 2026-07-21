<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Statement;

use Lagdo\DbAdmin\Driver\Sql\AbstractDbProxy;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDdDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\ForeignKeyDdDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\ForeignKeyDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableAlterDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableDdDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableDto;

use function array_filter;
use function array_map;
use function implode;
use function preg_match;
use function str_ireplace;

abstract class AbstractTable extends AbstractDbProxy implements TableInterface
{
    /**
     * @param TableDdDto $table
     *
     * @return bool
     */
    protected function primaryKeyChanged(TableDdDto $table): bool
    {
        return $table->primaryKeyChanged();
    }

    /**
     * @param TableDdDto $table
     * @param string $prefix
     *
     * @return array
     */
    protected function getCreatePrimaryKeyClause(TableDdDto $table, string $prefix = ''): array
    {
        if (!$this->primaryKeyChanged($table)) {
            return [];
        }
        $columns = array_filter($table->columns, fn(ColumnDto $column) => $column->primary);
        if (count($columns) === 0) {
            return [];
        }

        $columnNames = implode(', ', array_map(fn(ColumnDdDto $column) =>
            $this->_statement()->escapeId($column->name), $columns));
        return ["{$prefix}PRIMARY KEY ($columnNames)"];
    }

    /**
     * @param TableDdDto $table
     * @param ForeignKeyDdDto $foreignKey
     *
     * @return string
     */
    private function formatForeignKeyDd(TableDdDto $table, ForeignKeyDdDto $foreignKey): string
    {
        $source = $this->_statement()->escapeTableName($foreignKey->source);
        $table = $this->_statement()->escapeTableName($foreignKey->table);
        $target = $this->_statement()->escapeTableName($foreignKey->column);
        $query = "FOREIGN KEY ($source) REFERENCES $table($target)";

        $onActions = $this->_engine()->actions();
        if (preg_match("~^($onActions)\$~", $foreignKey->onUpdate)) {
            $query .= " ON UPDATE {$foreignKey->onUpdate}";
        }
        if (preg_match("~^($onActions)\$~", $foreignKey->onDelete)) {
            $query .= " ON DELETE {$foreignKey->onDelete}";
        }

        return $query;
    }

    /**
     * @param TableDdDto $table
     * @param string $prefix
     *
     * @return array<string>
     */
    protected function getCreateForeignKeyClauses(TableDdDto $table, string $prefix = ''): array
    {
        $filter = fn(ForeignKeyDdDto $foreignKey) =>
            $foreignKey->added() || $foreignKey->edited();
        $foreignKeys = array_filter($table->foreignKeys, $filter);
        $formatter = fn(ForeignKeyDdDto $foreignKey) =>
            $prefix . $this->formatForeignKeyDd($table, $foreignKey);
        return array_map($formatter, $foreignKeys);
    }

    /**
     * @param TableAlterDto $table
     *
     * @return array
     */
    abstract protected function getDropPrimaryKeyClause(TableAlterDto $table): array;

    /**
     * @param TableAlterDto $table
     *
     * @return array<string>
     */
    abstract protected function getDeleteForeignKeyClauses(TableAlterDto $table): array;

    /**
     * @param TableAlterDto $table
     *
     * @return array
     */
    protected function getDropConstraintsQuery(TableAlterDto $table): array
    {
        $clauses = [
            ...$this->getDropPrimaryKeyClause($table),
            ...$this->getDeleteForeignKeyClauses($table),
        ];
        if (count($clauses) === 0) {
            return [];
        }

        // Use the previous table name.
        $tableName = $this->_statement()->escapeTableName($table->statusName());
        return ["ALTER TABLE $tableName\n  " . implode(",\n  ", $clauses)];
    }

    /**
     * @param ForeignKeyDto $foreignKey
     *
     * @return array
     */
    private function fkColumns(ForeignKeyDto $foreignKey)
    {
        $escape = $this->_statement()->escapeId(...);
        return [
            implode(', ', array_map($escape, $foreignKey->source)),
            implode(', ', array_map($escape, $foreignKey->target)),
        ];
    }

    /**
     * @param ForeignKeyDto $foreignKey
     *
     * @return string
     */
    private function fkTablePrefix(ForeignKeyDto $foreignKey)
    {
        $prefix = '';
        if ($foreignKey->database !== '' && $foreignKey->database !== $this->_engine()->database()) {
            $prefix .= $this->_statement()->escapeId($foreignKey->database) . '.';
        }
        if ($foreignKey->schema !== '' && $foreignKey->schema !== $this->_engine()->schema()) {
            $prefix .= $this->_statement()->escapeId($foreignKey->schema) . '.';
        }
        return $prefix;
    }

    /**
     * @param ForeignKeyDto $foreignKey
     *
     * @return string
     */
    protected function formatForeignKey(ForeignKeyDto $foreignKey): string
    {
        [$sources, $targets] = $this->fkColumns($foreignKey);
        $onActions = $this->_engine()->actions();
        $query = "FOREIGN KEY ($sources) REFERENCES " . $this->fkTablePrefix($foreignKey) .
            $this->_statement()->escapeTableName($foreignKey->table) . " ($targets)";
        if (preg_match("~^($onActions)\$~", $foreignKey->onDelete)) {
            $query .= " ON DELETE {$foreignKey->onDelete}";
        }
        if (preg_match("~^($onActions)\$~", $foreignKey->onUpdate)) {
            $query .= " ON UPDATE {$foreignKey->onUpdate}";
        }

        return $query;
    }

    /**
     * @param TableDdDto $table
     * @param string $prefix
     *
     * @return array<string>
     */
    protected function getForeignKeyClauses(TableDdDto $table, string $prefix = ''): array
    {
        return array_map(fn(ForeignKeyDto $fkColumn) => $prefix .
            $this->formatForeignKey($fkColumn), $table->foreignKeys);
    }

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
    public function getCreateIndexQuery(string $table, string $type, string $name, string $columns): string
    {
        return '';
    }

    /**
     * Get default value clause
     *
     * @param ColumnDto $column
     *
     * @return string
     */
    protected function getDefaultValueClause(ColumnDto $column): string
    {
        return match(true) {
            $column->default === null => '',
            preg_match('~char|binary|text|enum|set~', $column->type) > 0,
            preg_match('~^(?![a-z])~i', $column->default) > 0 => ' DEFAULT ' .
                $this->_engine()->quote($column->default),
            default => " DEFAULT {$column->default}",
        };
    }

    /**
     * @param string $onUpdate
     *
     * @return string
     */
    protected function fixOnUpdateTimestamp(string $onUpdate): string
    {
        return str_ireplace("current_timestamp()", "CURRENT_TIMESTAMP", $onUpdate);
    }

    /**
     * @inheritDoc
     */
    protected function getPrimaryKeyModifier(ColumnDdDto $input, TableDdDto $table): string
    {
        return $input->primary && $table->primaryKeyColumnCount() === 1 ? ' PRIMARY KEY' : '';
    }

    /**
     * Generate column modifier for primary key, auto increment and index
     *
     * @param ColumnDdDto $input
     * @param TableDdDto $table
     *
     * @return string
     */
    abstract protected function getColumnModifier(ColumnDdDto $input, TableDdDto $table): string;

    /**
     * @param ColumnDdDto $input
     * @param TableDdDto $table
     *
     * @return string
     */
    protected function getAddColumnClause(ColumnDdDto $input, TableDdDto $table): string
    {
        $name = $this->_statement()->escapeId($input->name);
        $type = $this->_statement()->getColumnType($input->typeColumn ?? $input);

        $nullValue = $input->nullable ? ' NULL' : ' NOT NULL'; // NULL for timestamp
        $defaultValue = $this->getDefaultValueClause($input);
        $modifier = $this->getColumnModifier($input, $table);

        // MariaDB exports CURRENT_TIMESTAMP as a function.
        $onUpdate = $input->onUpdate !== '' && preg_match('~timestamp|datetime~', $type) ?
            ' ON UPDATE ' . $this->fixOnUpdateTimestamp($input->onUpdate) : '';
        $comment = $this->_engine()->support('comment') && $input->comment !== null ?
            ' COMMENT ' . $this->_engine()->quote($input->comment) : '';

        return "$name$type$nullValue$defaultValue$onUpdate$comment$modifier";
    }

    /**
     * @param TableAlterDto $table
     *
     * @return array<string>
     */
    protected function getDropColumnClauses(TableAlterDto $table): array
    {
        $dropColumnCallback = fn(ColumnDto $column) => 'DROP COLUMN ' .
            $this->_statement()->escapeId($column->name);
        return array_map($dropColumnCallback, $table->droppedColumns());
    }
}
