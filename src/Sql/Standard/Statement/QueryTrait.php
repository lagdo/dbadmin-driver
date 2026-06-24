<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Statement;

use Lagdo\DbAdmin\Driver\Sql\DbProxyTrait;
use Lagdo\DbAdmin\Driver\Sql\Dto\QueryClauseDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\SelectDto;

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
     * @param bool $grouped
     * @param array $groupBy
     *
     * @return string
     */
    public function getRowCountQuery(string $table, array $where, bool $grouped, array $groupBy): string
    {
        $query = ' FROM ' . $this->_statement()->escapeTableName($table);
        if (!empty($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }
        return ($grouped && ($this->_engine()->sql() || count($groupBy) == 1) ?
            'SELECT COUNT(DISTINCT ' . implode(', ', $groupBy) . ")$query" :
            'SELECT COUNT(*)' . ($grouped ? " FROM (SELECT 1$query GROUP BY " .
            implode(', ', $groupBy) . ') x' : $query)
        );
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
