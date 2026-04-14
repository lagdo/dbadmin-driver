<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Engine;

use Lagdo\DbAdmin\Driver\Sql\DbProxyTrait;
use Lagdo\DbAdmin\Driver\Sql\Connection\StatementInterface;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableFieldDto;
use Exception;

use function implode;
use function is_object;
use function is_string;
use function preg_match;
use function preg_replace;
use function strlen;
use function substr;

trait QueryTrait
{
    use DbProxyTrait;

    /**
     * Execute and remember query
     *
     * @param string $query
     *
     * @return StatementInterface|bool
     */
    public function execute(string $query): StatementInterface|bool
    {
        return $this->_engine()->connection()->query($query);
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
     * Select data from table
     *
     * @param string $table
     * @param array $select Result of processSelectColumns()[0]
     * @param array $where Result of processSelectWhere()
     * @param array $group Result of processSelectColumns()[1]
     * @param array $order Result of processSelectOrder()
     * @param int $limit Result of processSelectLimit()
     * @param int $page Index of page starting at zero
     *
     * @return StatementInterface|bool
     */
    public function select(string $table, array $select, array $where, array $group = [],
        array $order = [], int $limit = 1, int $page = 0): StatementInterface|bool
    {
        return $this->execute($this->_statement()->getRowSelectQuery($table, $select,
            $where, $group, $order, $limit, $page));
    }

    /**
     * Insert data into table
     *
     * @param string $table
     * @param array $values Escaped columns in keys, quoted data in values
     *
     * @return bool
     */
    public function insert(string $table, array $values): bool
    {
        return $this->execute($this->_statement()->getRowInsertQuery($table, $values)) !== false;
    }

    /**
     * Update data in table
     *
     * @param string $table
     * @param array $values Escaped columns in keys, quoted data in values
     * @param string $queryWhere " WHERE ..."
     * @param int $limit 0 or 1
     *
     * @return bool
     */
    public function update(string $table, array $values, string $queryWhere, int $limit = 0): bool
    {
        return $this->execute($this->_statement()->getRowUpdateQuery($table,
            $values, $queryWhere, $limit)) !== false;
    }

    /**
     * Delete data from table
     *
     * @param string $table
     * @param string $queryWhere " WHERE ..."
     * @param int $limit 0 or 1
     *
     * @return bool
     */
    public function delete(string $table, string $queryWhere, int $limit = 0): bool
    {
        return $this->execute($this->_statement()->getRowDeleteQuery($table,
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
            throw new Exception($this->_engine()->error() . $sql);
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
        $bUseSqlLike = $this->_engine()->sql() && is_numeric($value) && preg_match('~\.~', $value);
        return $column . match(true) {
            $bUseSqlLike => ' LIKE ' . $this->_engine()->quote($value),
            $this->_engine()->mssql() => // LIKE because of text
                ' LIKE ' . $this->_engine()->quote(preg_replace('~[_%[]~', '[\0]', $value)),
            //! enum and set
            default => ' = ' . $this->_statement()->unconvertField($field, $this->_engine()->quote($value)),
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
        $collate = $this->_engine()->sql() &&
            preg_match('~char|text~', $field->type) &&
            preg_match("~[^ -@]~", $value);
        return !$collate ? '' :
            // not just [a-z] to catch non-ASCII characters
            "$column = " . $this->_engine()->quote($value) . ' COLLATE ' . $this->_engine()->charset() . '_bin';
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
            return [$this->_statement()->escapeKey($column), $value];
        }

        $expr = $this->_statement()->bracketEscape($value['expr'], 1); // 1 - back
        return [$this->_statement()->escapeKey($expr), $value['value']];
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
            $clauses[] = $this->_statement()->escapeKey($column) . ' IS NULL';
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
     * Apply command to all array items
     *
     * @param string $query
     * @param array $tables
     * @param callback|null $escape
     *
     * @return bool
     */
    public function applyQueries(string $query, array $tables, $escape = null): bool
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
     * @param int $column
     *
     * @return array
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
     * Get list of values from database
     *
     * @param string $query
     * @param string $column
     *
     * @return array
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
     * Get keys from first column and values from second
     *
     * @param string $query
     * @param bool $setKeys
     *
     * @return array
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
