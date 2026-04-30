<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Statement;

use Lagdo\DbAdmin\Driver\Sql\DbProxyTrait;
use Lagdo\DbAdmin\Driver\Sql\Dto\SelectInputDto;

use function array_map;
use function array_keys;
use function count;
use function implode;
use function in_array;

trait QueryTrait
{
    use DbProxyTrait;

    /**
     * Get query to compute number of found rows
     *
     * @param string $table
     * @param array $where
     * @param bool $isGroup
     * @param array $groups
     *
     * @return string
     */
    public function getRowCountQuery(string $table, array $where, bool $isGroup, array $groups): string
    {
        $query = ' FROM ' . $this->_statement()->escapeTableName($table);
        if (!empty($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }
        return ($isGroup && ($this->_engine()->sql() || count($groups) == 1) ?
            'SELECT COUNT(DISTINCT ' . implode(', ', $groups) . ")$query" :
            'SELECT COUNT(*)' . ($isGroup ? " FROM (SELECT 1$query GROUP BY " .
            implode(', ', $groups) . ') x' : $query)
        );
    }

    /**
     * Build a query to select data from table
     *
     * @param string $table
     * @param array $columns Result of processSelectColumns()[0]
     * @param array $where Result of processSelectWhere()
     * @param array $group Result of processSelectColumns()[1]
     * @param array $order Result of processSelectOrder()
     * @param int $limit Result of processSelectLimit()
     * @param int $page Index of page starting at zero
     *
     * @return string
     */
    public function getRowSelectQuery(string $table, array $columns, array $where, array $group = [],
        array $order = [], int $limit = 1, int $page = 0): string
    {
        $input = new SelectInputDto($table, $columns, $where, $group, $order, $limit, $page);
        return $this->_statement()->getTableSelectQuery($input);
    }

    /**
     * Build a query to insert data into table
     *
     * @param string $table
     * @param array $values Escaped columns in keys, quoted data in values
     *
     * @return string
     */
    public function getRowInsertQuery(string $table, array $values): string
    {
        $table = $this->_statement()->escapeTableName($table);
        if (empty($values)) {
            return $this->_engine()->sql() ?
                "INSERT INTO $table () VALUES ()" :
                "INSERT INTO $table DEFAULT VALUES";
        }

        $columns = implode(', ', array_keys($values));
        $values = implode(', ', $values);
        return "INSERT INTO $table ($columns) VALUES ($values)";
    }

    /**
     * Build a query to update data in table
     *
     * @param string $table
     * @param array $values Escaped columns in keys, quoted data in values
     * @param string $queryWhere " WHERE ..."
     * @param int $limit 0 or 1
     *
     * @return string
     */
    public function getRowUpdateQuery(string $table, array $values, string $queryWhere, int $limit = 0): string
    {
        $callback = fn(string $value, string $name) => "$name = $value";
        $assignments = implode(', ', array_map($callback, $values, array_keys($values)));
        $query = $this->_statement()->escapeTableName($table) . " SET $assignments";

        return $limit <= 0 ? "UPDATE $query $queryWhere" : 'UPDATE' .
            $this->_statement()->limitToOne($table, $query, $queryWhere);
    }

    /**
     * Build a query to delete data from table
     *
     * @param string $table
     * @param string $queryWhere " WHERE ..."
     * @param int $limit 0 or 1
     *
     * @return string
     */
    public function getRowDeleteQuery(string $table, string $queryWhere, int $limit = 0): string
    {
        $query = 'FROM ' . $this->_statement()->escapeTableName($table);
        return $limit <= 0 ? "DELETE $query $queryWhere" : 'DELETE' .
            $this->_statement()->limitToOne($table, $query, $queryWhere);
    }

    /**
     * Get select clause for convertible columns
     *
     * @param array $names
     * @param array $columns
     * @param array $select
     *
     * @return string
     */
    public function convertValues(array $names, array $columns, array $select = []): string
    {
        $hasSelect = count($select) > 0;
        $clauses = array_map(function(string $name) use($hasSelect, $columns, $select) {
            $name = $this->_statement()->escapeId($name);
            if ($hasSelect && !in_array($name, $select)) {
                return null;
            }

            $columnName = $this->_statement()->convertValue($columns[$name]);
            return $columnName === '' ? null : ", $columnName AS $name";
        }, $names);

        $callback = fn(string|null $clause) => $clause !== null;
        return implode('', array_filter($clauses, $callback));
    }
}
