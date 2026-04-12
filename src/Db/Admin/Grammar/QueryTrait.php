<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Grammar;

use Lagdo\DbAdmin\Support\Db\DbProxyTrait;
use Lagdo\DbAdmin\Support\Dto\TableSelectDto;

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
        $query = ' FROM ' . $this->_grammar()->escapeTableName($table);
        if (!empty($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }
        return ($isGroup && ($this->_driver()->sql() || count($groups) == 1) ?
            'SELECT COUNT(DISTINCT ' . implode(', ', $groups) . ")$query" :
            'SELECT COUNT(*)' . ($isGroup ? " FROM (SELECT 1$query GROUP BY " .
            implode(', ', $groups) . ') x' : $query)
        );
    }

    /**
     * Build a query to select data from table
     *
     * @param string $table
     * @param array $select Result of processSelectColumns()[0]
     * @param array $where Result of processSelectWhere()
     * @param array $group Result of processSelectColumns()[1]
     * @param array $order Result of processSelectOrder()
     * @param int $limit Result of processSelectLimit()
     * @param int $page Index of page starting at zero
     *
     * @return string
     */
    public function getRowSelectQuery(string $table, array $select, array $where, array $group = [],
        array $order = [], int $limit = 1, int $page = 0): string
    {
        $entity = new TableSelectDto($table, $select, $where, $group, $order, $limit, $page);
        return $this->_grammar()->getTableSelectQuery($entity);
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
        $table = $this->_grammar()->escapeTableName($table);
        if (empty($values)) {
            return $this->_driver()->sql() ?
                "INSERT INTO $table () VALUES ()" :
                "INSERT INTO $table DEFAULT VALUES";
        }
        $fields = implode(', ', array_keys($values));
        $values = implode(', ', $values);
        return "INSERT INTO $table ($fields) VALUES ($values)";
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
        $assignments = [];
        foreach ($values as $name => $value) {
            $assignments[] = "$name = $value";
        }
        $query = $this->_grammar()->escapeTableName($table) . ' SET ' . implode(', ', $assignments);
        return $limit <= 0 ? "UPDATE $query $queryWhere" :
            'UPDATE' . $this->_grammar()->limitToOne($table, $query, $queryWhere);
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
        $query = 'FROM ' . $this->_grammar()->escapeTableName($table);
        return $limit <= 0 ? "DELETE $query $queryWhere" :
            'DELETE' . $this->_grammar()->limitToOne($table, $query, $queryWhere);
    }

    /**
     * Get select clause for convertible fields
     *
     * @param array $columns
     * @param array $fields
     * @param array $select
     *
     * @return string
     */
    public function convertFields(array $columns, array $fields, array $select = []): string
    {
        $clause = '';
        foreach ($columns as $key => $val) {
            if (!empty($select) && !in_array($this->_grammar()->escapeId($key), $select)) {
                continue;
            }
            $as = $this->_grammar()->convertField($fields[$key]);
            if ($as) {
                $clause .= ", $as AS " . $this->_grammar()->escapeId($key);
            }
        }
        return $clause;
    }
}
