<?php

namespace Lagdo\DbAdmin\Support\Db\Engine\Grammar;

use Lagdo\DbAdmin\Support\Db\AbstractDelegate;
use Lagdo\DbAdmin\Support\Dto\AbstractTableDto;
use Lagdo\DbAdmin\Support\Dto\ForeignKeyDto;
use Lagdo\DbAdmin\Support\Dto\TableDto;

use function array_map;
use function implode;
use function preg_match;

abstract class AbstractTable extends AbstractDelegate implements TableInterface
{
    /**
     * @param ForeignKeyDto $foreignKey
     *
     * @return array
     */
    private function fkFields(ForeignKeyDto $foreignKey)
    {
        $escape = $this->grammar->escapeId(...);
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
        if ($foreignKey->database !== '' && $foreignKey->database !== $this->driver->database()) {
            $prefix .= $this->grammar->escapeId($foreignKey->database) . '.';
        }
        if ($foreignKey->schema !== '' && $foreignKey->schema !== $this->driver->schema()) {
            $prefix .= $this->grammar->escapeId($foreignKey->schema) . '.';
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
        [$sources, $targets] = $this->fkFields($foreignKey);
        $onActions = $this->driver->actions();
        $query = "FOREIGN KEY ($sources) REFERENCES " . $this->fkTablePrefix($foreignKey) .
            $this->grammar->escapeTableName($foreignKey->table) . " ($targets)";
        if (preg_match("~^($onActions)\$~", $foreignKey->onDelete)) {
            $query .= " ON DELETE {$foreignKey->onDelete}";
        }
        if (preg_match("~^($onActions)\$~", $foreignKey->onUpdate)) {
            $query .= " ON UPDATE {$foreignKey->onUpdate}";
        }

        return $query;
    }

    /**
     * @param AbstractTableDto $table
     * @param string $prefix
     *
     * @return array<string>
     */
    protected function getForeignKeyClauses(AbstractTableDto $table, string $prefix = ''): array
    {
        return array_map(fn(ForeignKeyDto $fkField) =>
            $prefix . $this->formatForeignKey($fkField), $table->foreignKeys);
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
}
