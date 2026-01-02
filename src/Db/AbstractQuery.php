<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\Driver\QueryInterface;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Utils\Utils;
use Exception;

use function implode;
use function is_object;
use function is_string;
use function preg_match;
use function preg_replace;
use function strlen;
use function substr;

abstract class AbstractQuery implements QueryInterface
{
    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var Utils
     */
    protected $utils;

    /**
     * The constructor
     *
     * @param DriverInterface $driver
     * @param Utils $utils
     */
    public function __construct(DriverInterface $driver, Utils $utils)
    {
        $this->driver = $driver;
        $this->utils = $utils;
    }

    /**
     * @inheritDoc
     */
    public function select(string $table, array $select, array $where, array $group = [],
        array $order = [], int $limit = 1, int $page = 0): StatementInterface|bool
    {
        return $this->execute($this->driver->getSelectQuery($table, $select,
            $where, $group, $order, $limit, $page));
    }

    /**
     * @inheritDoc
     */
    public function insert(string $table, array $values): bool
    {
        return $this->execute($this->driver->getInsertQuery($table, $values)) !== false;
    }

    /**
     * @inheritDoc
     */
    public function update(string $table, array $values, string $queryWhere, int $limit = 0): bool
    {
        return $this->execute($this->driver->getUpdateQuery($table,
            $values, $queryWhere, $limit)) !== false;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $table, string $queryWhere, int $limit = 0): bool
    {
        return $this->execute($this->driver->getDeleteQuery($table,
            $queryWhere, $limit)) !== false;
    }

    /**
     * @inheritDoc
     */
    public function slowQuery(string $query, int $timeout): string|null
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function countRows(TableEntity $tableStatus, array $where): int|null
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function convertSearch(string $idf, array $value, TableFieldEntity $field): string
    {
        return $idf;
    }

    /**
     * @inheritDoc
     */
    public function view(string $name): array
    {
        return [];
    }

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
     * @param TableFieldEntity $field
     * @param string $column
     * @param string $value
     *
     * @return string
     */
    private function getWhereColumnClause(TableFieldEntity $field, string $column, string $value): string
    {
        $bUseSqlLike = $this->driver->jush() === 'sql' && is_numeric($value) && preg_match('~\.~', $value);
        return $column . match(true) {
            $bUseSqlLike => ' LIKE ' . $this->driver->quote($value),
            $this->driver->jush() === 'mssql' => // LIKE because of text
                ' LIKE ' . $this->driver->quote(preg_replace('~[_%[]~', '[\0]', $value)),
            //! enum and set
            default => ' = ' . $this->driver->unconvertField($field, $this->driver->quote($value)),
        };
    }

    /**
     * @param TableFieldEntity $field
     * @param string $column
     * @param string $value
     *
     * @return string
     */
    private function getWhereCollateClause(TableFieldEntity $field, string $column, string $value): string
    {
        $collate = $this->driver->jush() === 'sql' &&
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
            return [$this->driver->escapeKey($column), $value];
        }

        $expr = $this->driver->bracketEscape($value['expr'], 1); // 1 - back
        return [$this->driver->escapeKey($expr), $value['value']];
    }

    /**
     * Create SQL condition from parsed query string
     *
     * @param array $where Parsed query string
     * @param array<TableFieldEntity> $fields
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
            $clauses[] = $this->driver->escapeKey($column) . ' IS NULL';
        }
        return implode(' AND ', $clauses);
    }

    /**
     * @inheritDoc
     */
    public function applyQueries(string $query, array $tables, $escape = null): bool
    {
        if (!$escape) {
            $escape = fn ($table) => $this->driver->escapeTableName($table);
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
