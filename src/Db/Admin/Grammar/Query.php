<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Grammar;

use Lagdo\DbAdmin\Support\Db\AbstractDelegate;
use Lagdo\DbAdmin\Support\Dto\TableSelectDto;

use function array_keys;
use function count;
use function implode;
use function in_array;

class Query extends AbstractDelegate implements QueryInterface
{
    /**
     * @inheritDoc
     */
    public function getRowCountQuery(string $table, array $where, bool $isGroup, array $groups): string
    {
        $query = ' FROM ' . $this->grammar->escapeTableName($table);
        if (!empty($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }
        return ($isGroup && ($this->driver->sql() || count($groups) == 1) ?
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
        return $this->grammar->getTableSelectQuery($entity);
    }

    /**
     * @inheritDoc
     */
    public function getRowInsertQuery(string $table, array $values): string
    {
        $table = $this->grammar->escapeTableName($table);
        if (empty($values)) {
            return $this->driver->sql() ?
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
            'UPDATE' . $this->grammar->limitToOne($table, $query, $queryWhere);
    }

    /**
     * @inheritDoc
     */
    public function getRowDeleteQuery(string $table, string $queryWhere, int $limit = 0): string
    {
        $query = 'FROM ' . $this->grammar->escapeTableName($table);
        return $limit <= 0 ? "DELETE $query $queryWhere" :
            'DELETE' . $this->grammar->limitToOne($table, $query, $queryWhere);
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
            $as = $this->grammar->convertField($fields[$key]);
            if ($as) {
                $clause .= ", $as AS " . $this->grammar->escapeId($key);
            }
        }
        return $clause;
    }
}
