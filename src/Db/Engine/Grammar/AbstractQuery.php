<?php

namespace Lagdo\DbAdmin\Support\Db\Engine\Grammar;

use Lagdo\DbAdmin\Support\DriverInterface;
use Lagdo\DbAdmin\Support\Db\Admin\Grammar\QueryInterface;
use Lagdo\DbAdmin\Support\Dto\TableSelectDto;
use Lagdo\DbAdmin\Support\Dto\TableFieldDto;
use Lagdo\DbAdmin\Support\GrammarInterface;
use Lagdo\DbAdmin\Support\Utils\Utils;

use function array_keys;
use function count;
use function implode;
use function in_array;

abstract class AbstractQuery implements QueryInterface
{
    /**
     * @param DriverInterface $driver
     * @param GrammarInterface $grammar
     * @param Utils $utils
     */
    public function __construct(protected DriverInterface $driver,
        protected GrammarInterface $grammar, protected Utils $utils)
    {}

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
    protected function getLimitClause(string $query, string $where, int $limit, int $offset = 0): string
    {
        return match(true) {
            $limit <= 0 => " $query$where",
            $offset <= 0 => " $query$where LIMIT $limit",
            default => " $query$where LIMIT $limit OFFSET $offset",
        };
    }

    /**
     * @inheritDoc
     */
    public function getTableSelectQuery(TableSelectDto $select): string
    {
        $query = implode(', ', $select->fields) .
            ' FROM ' . $this->grammar->escapeTableName($select->table);
        $limit = +$select->limit;
        $offset = $select->page ? $limit * $select->page : 0;

        return 'SELECT' . $this->getLimitClause($query, $select->clauses, $limit, $offset);
    }

    /**
     * @inheritDoc
     */
    public function getRowCountQuery(string $table, array $where, bool $isGroup, array $groups): string
    {
        $query = ' FROM ' . $this->grammar->escapeTableName($table);
        if (!empty($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }
        return ($isGroup && ($this->driver->jush() == 'sql' || count($groups) == 1) ?
            'SELECT COUNT(DISTINCT ' . implode(', ', $groups) . ")$query" :
            'SELECT COUNT(*)' . ($isGroup ? " FROM (SELECT 1$query GROUP BY " .
            implode(', ', $groups) . ') x' : $query)
        );
    }

    /**
     * @inheritDoc
     */
    public function getRowSelectQuery(string $table, array $select, array $where, array $group = [],
        array $order = [], int $limit = 1, int $page = 0): string
    {
        $entity = new TableSelectDto($table, $select, $where, $group, $order, $limit, $page);
        return $this->getTableSelectQuery($entity);
    }

    /**
     * @inheritDoc
     */
    public function getRowInsertQuery(string $table, array $values): string
    {
        $table = $this->grammar->escapeTableName($table);
        if (empty($values)) {
            return $this->driver->jush() === 'mysql' ?
                "INSERT INTO $table () VALUES ()" :
                "INSERT INTO $table DEFAULT VALUES";
        }
        $fields = implode(', ', array_keys($values));
        $values = implode(', ', $values);
        return "INSERT INTO $table ($fields) VALUES ($values)";
    }

    /**
     * @inheritDoc
     */
    public function getRowUpdateQuery(string $table, array $values, string $queryWhere, int $limit = 0): string
    {
        $assignments = [];
        foreach ($values as $name => $value) {
            $assignments[] = "$name = $value";
        }
        $query = $this->grammar->escapeTableName($table) . ' SET ' . implode(', ', $assignments);
        return $limit <= 0 ? "UPDATE $query $queryWhere" :
            'UPDATE' . $this->limitToOne($table, $query, $queryWhere);
    }

    /**
     * @inheritDoc
     */
    public function getRowDeleteQuery(string $table, string $queryWhere, int $limit = 0): string
    {
        $query = 'FROM ' . $this->grammar->escapeTableName($table);
        return $limit <= 0 ? "DELETE $query $queryWhere" :
            'DELETE' . $this->limitToOne($table, $query, $queryWhere);
    }

    /**
     * @inheritDoc
     */
    public function convertField(TableFieldDto $field): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function unconvertField(TableFieldDto $field, string $value): string
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function convertFields(array $columns, array $fields, array $select = []): string
    {
        $clause = '';
        foreach ($columns as $key => $val) {
            if (!empty($select) && !in_array($this->grammar->escapeId($key), $select)) {
                continue;
            }
            $as = $this->convertField($fields[$key]);
            if ($as) {
                $clause .= ", $as AS " . $this->grammar->escapeId($key);
            }
        }
        return $clause;
    }
}
