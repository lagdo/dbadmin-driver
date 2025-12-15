<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\Driver\QueryInterface;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Entity\TableSelectEntity;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\Utils\Utils;
use Exception;

use function implode;
use function is_object;
use function array_keys;
use function preg_match;
use function preg_replace;
use function substr;
use function strlen;

abstract class Query implements QueryInterface
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
     * Formulate SQL modification query with limit 1
     *
     * @param string $table
     * @param string $query Everything after UPDATE or DELETE
     * @param string $where
     *
     * @return string
     */
    abstract protected function limitToOne(string $table, string $query, string $where): string;

    /**
     * @inheritDoc
     */
    public function select(string $table, array $select, array $where, array $group,
        array $order = [], int $limit = 1, int $page = 0): StatementInterface|bool
    {
        $entity = new TableSelectEntity($table, $select,
            $where, $group, $order, $limit, $page);
        $query = $this->driver->buildSelectQuery($entity);
        return $this->execute($query);
    }

    /**
     * @inheritDoc
     */
    public function insert(string $table, array $values): bool
    {
        $table = $this->driver->escapeTableName($table);
        if (empty($values)) {
            $result = $this->execute("INSERT INTO $table DEFAULT VALUES");
            return $result !== false;
        }
        $result = $this->execute("INSERT INTO $table (" .
            implode(', ', array_keys($values)) .
            ') VALUES (' . implode(', ', $values) . ')');
        return $result !== false;
    }

    /**
     * @inheritDoc
     */
    public function update(string $table, array $values, string $queryWhere, int $limit = 0): bool
    {
        $assignments = [];
        foreach ($values as $name => $value) {
            $assignments[] = "$name = $value";
        }
        $query = $this->driver->escapeTableName($table) . ' SET ' . implode(', ', $assignments);
        if (!$limit) {
            $result = $this->execute('UPDATE ' . $query . $queryWhere);
            return $result !== false;
        }
        $result = $this->execute('UPDATE' . $this->limitToOne($table, $query, $queryWhere));
        return $result !== false;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $table, string $queryWhere, int $limit = 0): bool
    {
        $query = 'FROM ' . $this->driver->escapeTableName($table);
        if (!$limit) {
            $result = $this->execute("DELETE $query $queryWhere");
            return $result !== false;
        }
        $result = $this->execute('DELETE' . $this->limitToOne($table, $query, $queryWhere));
        return $result !== false;
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
        return $column . ($bUseSqlLike ?
            // LIKE because of floats but slow with ints
            " LIKE " . $this->driver->quote($value) :
            ($this->driver->jush() === 'mssql' ?
                // LIKE because of text
                " LIKE " . $this->driver->quote(preg_replace('~[_%[]~', '[\0]', $value)) :
                //! enum and set
                " = " . $this->driver->unconvertField($field, $this->driver->quote($value))));
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
        $bCollate = $this->driver->jush() === 'sql' &&
            preg_match('~char|text~', $field->type) && preg_match("~[^ -@]~", $value);
        return !$bCollate ? '' :
            // not just [a-z] to catch non-ASCII characters
            "$column = " . $this->driver->quote($value) . " COLLATE " . $this->driver->charset() . "_bin";
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
        $wheres = $where["where"] ?? [];
        foreach ((array) $wheres as $key => $value) {
            $key = $this->driver->bracketEscape($key, 1); // 1 - back
            $column = $this->driver->escapeKey($key);
            $clauses[] = $this->getWhereColumnClause($fields[$key], $column, $value);
            if (($clause = $this->getWhereCollateClause($fields[$key], $column, $value))) {
                $clauses[] = $clause;
            }
        }
        $nulls = $where["null"] ?? [];
        foreach ((array) $nulls as $key) {
            $clauses[] = $this->driver->escapeKey($key) . " IS NULL";
        }
        return implode(" AND ", $clauses);
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
