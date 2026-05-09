<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Statement;

use Lagdo\DbAdmin\Driver\Sql\AbstractDbProxy;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnInputDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\ForeignKeyDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableDdlDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableDto;

use function array_map;
use function implode;
use function preg_match;

abstract class AbstractTable extends AbstractDbProxy implements TableInterface
{
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
     * @param TableDdlDto $table
     * @param string $prefix
     *
     * @return array<string>
     */
    protected function getForeignKeyClauses(TableDdlDto $table, string $prefix = ''): array
    {
        $formatter = fn(ForeignKeyDto $fkColumn) => $prefix . $this->formatForeignKey($fkColumn);
        return array_map($formatter, $table->foreignKeys);
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
            preg_match('~^(?![a-z])~i', $column->default) > 0 =>
                ' DEFAULT ' . $this->_engine()->quote($column->default),
            default => " DEFAULT {$column->default}",
        };
    }

    /**
     * @param string $onUpdate
     *
     * @return string
     */
    private function fixOnUpdateTimestamp(string $onUpdate): string
    {
        return str_ireplace("current_timestamp()", "CURRENT_TIMESTAMP", $onUpdate);
    }

    /**
     * @return string
     */
    protected function getAddColumnClause(ColumnInputDto $input): string
    {
        $name = $this->_statement()->escapeId($input->name);
        $type = $this->_statement()->getColumnType($input->typeColumn ?? $input);

        $nullValue = $input->nullable ? ' NULL' : ' NOT NULL'; // NULL for timestamp
        $defaultValue = $this->getDefaultValueClause($input);
        $autoIncrement = $input->autoIncrement ?
            $this->_statement()->getAutoIncrementModifier() : '';

        // MariaDB exports CURRENT_TIMESTAMP as a function.
        $onUpdate = $input->onUpdate === '' || !preg_match('~timestamp|datetime~', $type) ?
            '' : ' ON UPDATE ' . $this->fixOnUpdateTimestamp($input->onUpdate);
        $comment = $this->_engine()->support('comment') && $input->comment !== null ?
            ' COMMENT ' . $this->_engine()->quote($input->comment) : '';

        return "$name$type$nullValue$defaultValue$onUpdate$comment$autoIncrement";
    }

    /**
     * @return string
     */
    protected function getEditColumnClause(ColumnInputDto $input): string
    {
        $name = $this->_statement()->escapeId($input->name);
        $type = $this->_statement()->getColumnType($input->typeColumn ?? $input);

        $nullValue = $input->nullable ? ' NULL' : ' NOT NULL'; // NULL for timestamp
        $defaultValue = $this->getDefaultValueClause($input);
        $autoIncrement = $input->autoIncrement ?
            $this->_statement()->getAutoIncrementModifier() : '';

        // MariaDB exports CURRENT_TIMESTAMP as a function.
        $onUpdate = $input->onUpdate === '' || !preg_match('~timestamp|datetime~', $type) ?
            '' : ' ON UPDATE ' . $this->fixOnUpdateTimestamp($input->onUpdate);
        $comment = $this->_engine()->support('comment') && $input->comment !== null ?
            ' COMMENT ' . $this->_engine()->quote($input->comment) : '';

        return "$name$type$nullValue$defaultValue$onUpdate$comment$autoIncrement";
    }
}
