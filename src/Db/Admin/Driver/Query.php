<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Driver;

use Lagdo\DbAdmin\Support\Db\AbstractDelegate;
use Lagdo\DbAdmin\Support\Db\Engine\Connection\StatementInterface;
use Lagdo\DbAdmin\Support\Db\Admin\Driver\QueryInterface;
use Lagdo\DbAdmin\Support\Dto\TableFieldDto;
use Exception;

use function implode;
use function is_object;
use function is_string;
use function preg_match;
use function preg_replace;
use function strlen;
use function substr;

class Query extends AbstractDelegate implements QueryInterface
{
    /**
     * @inheritDoc
     */
    public function execute(string $query): StatementInterface|bool
    {
        return $this->driver->connection()->query($query);
    }

    /**
     * @inheritDoc
     */
    public function begin(): bool
    {
        return $this->execute("BEGIN") !== false;
    }

    /**
     * @inheritDoc
     */
    public function commit(): bool
    {
        return $this->execute("COMMIT") !== false;
    }

    /**
     * @inheritDoc
     */
    public function rollback(): bool
    {
        return $this->execute("ROLLBACK") !== false;
    }

    /**
     * @inheritDoc
     */
    public function select(string $table, array $select, array $where, array $group = [],
        array $order = [], int $limit = 1, int $page = 0): StatementInterface|bool
    {
        return $this->execute($this->grammar->getRowSelectQuery($table, $select,
            $where, $group, $order, $limit, $page));
    }

    /**
     * @inheritDoc
     */
    public function insert(string $table, array $values): bool
    {
        return $this->execute($this->grammar->getRowInsertQuery($table, $values)) !== false;
    }

    /**
     * @inheritDoc
     */
    public function update(string $table, array $values, string $queryWhere, int $limit = 0): bool
    {
        return $this->execute($this->grammar->getRowUpdateQuery($table,
            $values, $queryWhere, $limit)) !== false;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $table, string $queryWhere, int $limit = 0): bool
    {
        return $this->execute($this->grammar->getRowDeleteQuery($table,
            $queryWhere, $limit)) !== false;
    }

    /**
     * Query printed after execution in the message
     *
     * @param string $query Executed query
     *
     * @return string
     */
    private function queryToLog(string $query/*, string $time*/): string
    {
        if (strlen($query) > 1e6) {
            // [\x80-\xFF] - valid UTF-8, \n - can end by one-line comment
            $query = preg_replace('~[\x80-\xFF]+$~', '', substr($query, 0, 1e6)) . "\n…";
        }
        return $query;
    }

    /**
     * Execute query
     *
     * @param string $query
     * @param bool $execute
     * @param bool $failed
     *
     * @return bool
     * @throws Exception
     */
    public function executeQuery(string $query, bool $execute = true,
        bool $failed = false/*, string $time = ''*/): bool
    {
        if ($execute) {
            // $start = microtime(true);
            $failed = !$this->execute($query);
            // $time = $this->trans->formatTime($start);
        }
        if ($failed) {
            $sql = '';
            if ($query) {
                $sql = $this->queryToLog($query/*, $time*/);
            }
            throw new Exception($this->driver->error() . $sql);
        }
        return true;
    }

    /**
     * @param TableFieldDto $field
     * @param string $column
     * @param string $value
     *
     * @return string
     */
    private function getWhereColumnClause(TableFieldDto $field, string $column, string $value): string
    {
        $bUseSqlLike = $this->driver->sql() && is_numeric($value) && preg_match('~\.~', $value);
        return $column . match(true) {
            $bUseSqlLike => ' LIKE ' . $this->driver->quote($value),
            $this->driver->mssql() => // LIKE because of text
                ' LIKE ' . $this->driver->quote(preg_replace('~[_%[]~', '[\0]', $value)),
            //! enum and set
            default => ' = ' . $this->grammar->unconvertField($field, $this->driver->quote($value)),
        };
    }

    /**
     * @param TableFieldDto $field
     * @param string $column
     * @param string $value
     *
     * @return string
     */
    private function getWhereCollateClause(TableFieldDto $field, string $column, string $value): string
    {
        $collate = $this->driver->sql() &&
            preg_match('~char|text~', $field->type) &&
            preg_match("~[^ -@]~", $value);
        return !$collate ? '' :
            // not just [a-z] to catch non-ASCII characters
            "$column = " . $this->driver->quote($value) . ' COLLATE ' . $this->driver->charset() . '_bin';
    }

    /**
     * @param string $column
     * @param string|array $value
     *
     * @return array
     */
    private function getWhereClauseValues(string $column, string|array $value): array
    {
        if (is_string($value)) {
            return [$this->grammar->escapeKey($column), $value];
        }

        $expr = $this->grammar->bracketEscape($value['expr'], 1); // 1 - back
        return [$this->grammar->escapeKey($expr), $value['value']];
    }

    /**
     * Create SQL condition from parsed query string
     *
     * @param array $where Parsed query string
     * @param array<TableFieldDto> $fields
     *
     * @return string
     */
    public function where(array $where, array $fields = []): string
    {
        $clauses = [];
        $wheres = $where['where'] ?? [];
        foreach ((array) $wheres as $column => $value) {
            $field = $fields[$column];
            [$column, $value] = $this->getWhereClauseValues($column, $value);

            $clauses[] = $this->getWhereColumnClause($field, $column, $value);
            if (($clause = $this->getWhereCollateClause($field, $column, $value))) {
                $clauses[] = $clause;
            }
        }
        $nulls = $where['null'] ?? [];
        foreach ((array) $nulls as $column) {
            $clauses[] = $this->grammar->escapeKey($column) . ' IS NULL';
        }
        return implode(' AND ', $clauses);
    }

    /**
     * @inheritDoc
     */
    public function rows(string $query): array
    {
        $statement = $this->execute($query);
        if (!is_object($statement)) { // can return true
            return [];
        }
        $rows = [];
        while ($row = $statement->fetchAssoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * @inheritDoc
     */
    public function applyQueries(string $query, array $tables, $escape = null): bool
    {
        if (!$escape) {
            $escape = $this->grammar->escapeTableName(...);
        }
        foreach ($tables as $table) {
            if (!$this->execute("$query " . $escape($table))) {
                return false;
            }
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function values(string $query, int $column = 0): array
    {
        $statement = $this->execute($query);
        if (!is_object($statement)) {
            return [];
        }
        $values = [];
        while ($row = $statement->fetchRow()) {
            $values[] = $row[$column];
        }
        return $values;
    }

    /**
     * @inheritDoc
     */
    public function colValues(string $query, string $column): array
    {
        $statement = $this->execute($query);
        if (!is_object($statement)) {
            return [];
        }
        $values = [];
        while ($row = $statement->fetchAssoc()) {
            $values[] = $row[$column];
        }
        return $values;
    }

    /**
     * @inheritDoc
     */
    public function keyValues(string $query, bool $setKeys = true): array
    {
        $statement = $this->execute($query);
        if (!is_object($statement)) {
            return [];
        }
        $values = [];
        while ($row = $statement->fetchRow()) {
            if ($setKeys) {
                $values[$row[0]] = $row[1];
            } else {
                $values[] = $row[0];
            }
        }
        return $values;
    }
}
