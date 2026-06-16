<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Statement;

use Lagdo\DbAdmin\Driver\Sql\DbProxyTrait;
use Lagdo\DbAdmin\Driver\Sql\Dto\SelectInputDto;

use function array_filter;
use function array_keys;
use function array_map;
use function count;
use function implode;
use function in_array;
use function preg_match;
use function strtoupper;
use function trim;
use function uniqid;

trait QueryTrait
{
    use DbProxyTrait;

    /**
     * @var bool
     */
    protected bool $setCharset = false;

    /**
     * Check if utf8mb4 might be needed
     *
     * @param string $create
     *
     * @return void
     */
    public function setUtf8mb4(string $create): void
    {
        // possible false positive
        if (!$this->setCharset && preg_match('~\butf8mb4~i', $create)) {
            $this->setCharset = true;
        }
    }

    /**
     * Get SET NAMES query, if utf8mb4 might be needed
     *
     * @return string
     */
    public function getCharsetQuery(): string
    {
        return !$this->setCharset ? '' : 'SET NAMES ' . $this->_engine()->charset() . ";\n\n";
    }

    /**
     * Command to update a view
     *
     * @param string $view The view name
     * @param array $values The view values
     *
     * @return array<string>
     */
    public function getUpdateViewQueries(string $view, array $values): array
    {
        // From view.inc.php
        $origType = 'VIEW';
        if ($this->_engine()->pgsql()) {
            $status = $this->_engine()->tableStatus($view);
            $origType = strtoupper($status->engine);
        }

        $name = trim($values['name']);
        $type = $values['materialized'] ? 'MATERIALIZED VIEW' : 'VIEW';
        $tempName = "{$name}_dbadmin_" . uniqid();

        $view = $this->_statement()->escapeTableName($view);
        $name = $this->_statement()->escapeTableName($name);
        $tempName = $this->_statement()->escapeTableName($tempName);
        return [
            "DROP $origType $view",
            "CREATE $type $name AS\n" . $values['select'],
            "DROP $type $name",
            "CREATE $type $tempName AS\n" . $values['select'],
            "DROP $type $tempName",
        ];
    }

    /**
     * Command to drop a view
     *
     * @param string $view The view name
     *
     * @return string
     */
    public function getDropViewQuery(string $view): string
    {
        // From view.inc.php
        $origType = 'VIEW';
        if ($this->_engine()->pgsql()) {
            $status = $this->_engine()->tableStatus($view);
            $origType = strtoupper($status->engine);
        }

        return "DROP $origType " . $this->_statement()->escapeTableName($view);
    }

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
    public function getSelectRowQuery(string $table, array $columns, array $where, array $group = [],
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
    public function getInsertRowQuery(string $table, array $values): string
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
    public function getUpdateRowQuery(string $table, array $values, string $queryWhere, int $limit = 0): string
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
    public function getDeleteRowQuery(string $table, string $queryWhere, int $limit = 0): string
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
    public function convertColumns(array $names, array $columns, array $select = []): string
    {
        $hasSelect = count($select) > 0;
        $clauses = array_map(function(string $name) use($hasSelect, $columns, $select) {
            $escapedName = $this->_statement()->escapeId($name);
            if ($hasSelect && !in_array($escapedName, $select)) {
                return null;
            }

            $columnClause = $this->_statement()->convertColumn($columns[$name]);
            return $columnClause === '' ? null : ", $columnClause AS $escapedName";
        }, $names);

        $callback = fn(string|null $clause) => $clause !== null;
        return implode('', array_filter($clauses, $callback));
    }
}
