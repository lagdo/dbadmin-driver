<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Exception;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Entity\TableSelectEntity;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\AdminInterface;
use Lagdo\DbAdmin\Driver\TranslatorInterface;

use function implode;
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
     * @var AdminInterface
     */
    protected $admin;

    /**
     * @var TranslatorInterface
     */
    protected $trans;

    /**
     * The constructor
     *
     * @param DriverInterface $driver
     * @param AdminInterface $admin
     * @param TranslatorInterface $trans
     */
    public function __construct(DriverInterface $driver, AdminInterface $admin, TranslatorInterface $trans)
    {
        $this->driver = $driver;
        $this->admin = $admin;
        $this->trans = $trans;
    }

    /**
     * @inheritDoc
     */
    public function schema()
    {
        return '';
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
    abstract protected function limitToOne(string $table, string $query, string $where);

    /**
     * @inheritDoc
     */
    public function select(string $table, array $select, array $where,
        array $group, array $order = [], int $limit = 1, int $page = 0)
    {
        $entity = new TableSelectEntity($table, $select, $where, $group, $order, $limit, $page);
        $query = $this->driver->buildSelectQuery($entity);
        // $this->start = intval(microtime(true));
        return $this->driver->execute($query);
    }

    /**
     * @inheritDoc
     */
    public function insert(string $table, array $values)
    {
        $table = $this->driver->table($table);
        if (empty($values)) {
            $result = $this->driver->execute("INSERT INTO $table DEFAULT VALUES");
            return $result !== false;
        }
        $result = $this->driver->execute("INSERT INTO $table (" .
            implode(', ', array_keys($values)) . ') VALUES (' . implode(', ', $values) . ')');
        return $result !== false;
    }

    /**
     * @inheritDoc
     */
    public function update(string $table, array $values, string $queryWhere, int $limit = 0)
    {
        $assignments = [];
        foreach ($values as $name => $value) {
            $assignments[] = "$name = $value";
        }
        $query = $this->driver->table($table) . ' SET ' . implode(', ', $assignments);
        if (!$limit) {
            $result = $this->driver->execute('UPDATE ' . $query . $queryWhere);
            return $result !== false;
        }
        $result = $this->driver->execute('UPDATE' . $this->limitToOne($table, $query, $queryWhere));
        return $result !== false;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $table, string $queryWhere, int $limit = 0)
    {
        $query = 'FROM ' . $this->driver->table($table);
        if (!$limit) {
            $result = $this->driver->execute("DELETE $query $queryWhere");
            return $result !== false;
        }
        $result = $this->driver->execute('DELETE' . $this->limitToOne($table, $query, $queryWhere));
        return $result !== false;
    }

    /**
     * @inheritDoc
     */
    public function explain(ConnectionInterface $connection, string $query)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function slowQuery(string $query, int $timeout)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function countRows(TableEntity $tableStatus, array $where)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function convertSearch(string $idf, array $val, TableFieldEntity $field)
    {
        return $idf;
    }

    /**
     * @inheritDoc
     */
    public function view(string $name)
    {
        return [];
    }

    /**
     * Remove current user definer from SQL command
     *
     * @param string $query
     *
     * @return string
     */
    public function removeDefiner(string $query): string
    {
        return preg_replace('~^([A-Z =]+) DEFINER=`' .
            preg_replace('~@(.*)~', '`@`(%|\1)', $this->user()) .
            '`~', '\1', $query); //! proper escaping of user
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
            $failed = !$this->driver->execute($query);
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
            $key = $this->admin->bracketEscape($key, 1); // 1 - back
            $column = $this->admin->escapeKey($key);
            $clauses[] = $this->getWhereColumnClause($fields[$key], $column, $value);
            if (($clause = $this->getWhereCollateClause($fields[$key], $column, $value))) {
                $clauses[] = $clause;
            }
        }
        $nulls = $where["null"] ?? [];
        foreach ((array) $nulls as $key) {
            $clauses[] = $this->admin->escapeKey($key) . " IS NULL";
        }
        return implode(" AND ", $clauses);
    }
}
