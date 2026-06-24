<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Engine;

use Lagdo\DbAdmin\Driver\Sql\DbProxyTrait;
use Lagdo\DbAdmin\Driver\Sql\Connection\QueryResultInterface;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;
use Closure;

use function implode;
use function is_int;
use function is_string;
use function preg_match;
use function preg_replace;

trait QueryTrait
{
    use DbProxyTrait;

    /**
     * Execute a query and return a boolean
     *
     * @param string $query
     *
     * @return bool
     */
    public function execute(string $query): bool
    {
        return !$this->_engine()->connection()->executeQuery($query)->hasError();
    }

    /**
     * Begin transaction
     *
     * @return bool
     */
    public function begin(): bool
    {
        return $this->execute("BEGIN") !== false;
    }

    /**
     * Commit transaction
     *
     * @return bool
     */
    public function commit(): bool
    {
        return $this->execute("COMMIT") !== false;
    }

    /**
     * Rollback transaction
     *
     * @return bool
     */
    public function rollback(): bool
    {
        return $this->execute("ROLLBACK") !== false;
    }

    /**
     * @param ColumnDto $column
     * @param string $name
     * @param string $value
     *
     * @return string
     */
    private function getWhereColumnClause(ColumnDto $column, string $name, string $value): string
    {
        $bUseSqlLike = $this->_engine()->sql() && is_numeric($value) && preg_match('~\.~', $value);
        return $name . match(true) {
            $bUseSqlLike => ' LIKE ' . $this->_engine()->quote($value),
            $this->_engine()->mssql() => // LIKE because of text
                ' LIKE ' . $this->_engine()->quote(preg_replace('~[_%[]~', '[\0]', $value)),
            //! enum and set
            default => ' = ' . $this->_statement()->unconvertColumn($column, $this->_engine()->quote($value)),
        };
    }

    /**
     * @param ColumnDto $column
     * @param string $name
     * @param string $value
     *
     * @return string
     */
    private function getWhereCollateClause(ColumnDto $column, string $name, string $value): string
    {
        $collate = $this->_engine()->sql() &&
            preg_match('~char|text~', $column->type) && preg_match("~[^ -@]~", $value);
        if (!$collate) {
            return '';
        }

        $value = $this->_engine()->quote($value);
        $charset = $this->_engine()->charset();
        // not just [a-z] to catch non-ASCII characters
        return "$name = $value COLLATE {$charset}_bin";
    }

    /**
     * @param string $name
     * @param string|array $value
     *
     * @return array
     */
    private function getWhereClauseValues(string $name, string|array $value): array
    {
        if (is_string($value)) {
            return [$this->_statement()->escapeKey($name), $value];
        }

        $expr = $this->_statement()->bracketEscape($value['expr'], 1); // 1 - back
        return [$this->_statement()->escapeKey($expr), $value['value']];
    }

    /**
     * Create SQL condition from parsed query string
     *
     * @param array $where Parsed query string
     * @param array<ColumnDto> $columns
     *
     * @return string
     */
    public function where(array $where, array $columns = []): string
    {
        $clauses = [];
        $wheres = $where['where'] ?? [];
        foreach ((array) $wheres as $name => $value) {
            $column = $columns[$name];
            [$name, $value] = $this->getWhereClauseValues($name, $value);

            $clauses[] = $this->getWhereColumnClause($column, $name, $value);
            if (($clause = $this->getWhereCollateClause($column, $name, $value))) {
                $clauses[] = $clause;
            }
        }
        $nulls = $where['null'] ?? [];
        foreach ((array) $nulls as $name) {
            $clauses[] = $this->_statement()->escapeKey($name) . ' IS NULL';
        }
        return implode(' AND ', $clauses);
    }

    /**
     * Get all rows of result
     *
     * @param string $query
     *
     * @return array
     */
    public function rows(string $query): array
    {
        $result = $this->executeQuery($query);
        if (!$result->hasRowset()) { // can return true
            return [];
        }

        $rows = [];
        while ($row = $result->fetchAssoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * Apply command to all array items
     *
     * @param string $query
     * @param array $tables
     * @param Closure|null $escape
     *
     * @return bool
     */
    public function applyQueries(string $query, array $tables, Closure|null $escape = null): bool
    {
        if (!$escape) {
            $escape = $this->_statement()->escapeTableName(...);
        }
        foreach ($tables as $table) {
            if (!$this->execute("$query " . $escape($table))) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get list of values from database
     *
     * @param string $query
     * @param string|int $column
     *
     * @return array
     */
    public function columnValues(string $query, string|int $column = -1): array
    {
        $colIsInt = is_int($column);
        if ($colIsInt && $column < 0) {
            $column = 0;
        }
        $result = $this->executeQuery($query);
        if (!$result->hasRowset()) {
            return [];
        }

        $fetchRow = $colIsInt ? $result->fetchRow(...) : $result->fetchAssoc(...);
        $values = [];
        while ($row = $fetchRow()) {
            $values[] = $row[$column];
        }
        return $values;
    }

    /**
     * Get a value from database
     *
     * @param string $query
     * @param string|int $column
     *
     * @return mixed
     */
    public function columnValue(string $query, string|int $column = -1): mixed
    {
        $colIsInt = is_int($column);
        if ($colIsInt && $column < 0) {
            $column = 0;
        }
        $result = $this->executeQuery($query);
        if (!$result->hasRowset()) {
            return null;
        }

        $row = $colIsInt ? $result->fetchRow() : $result->fetchAssoc();
        return $row[$column] ?? null;
    }

    /**
     * Get keys from first column and values from second
     *
     * @param string $query
     * @param bool $setKeys
     *
     * @return array
     */
    public function keyValues(string $query, bool $setKeys = true): array
    {
        $result = $this->executeQuery($query);
        if (!$result->hasRowset()) {
            return [];
        }

        $values = [];
        while ($row = $result->fetchRow()) {
            if ($setKeys) {
                $values[$row[0]] = $row[1];
            } else {
                $values[] = $row[0];
            }
        }
        return $values;
    }
}
