<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Statement;

use Lagdo\DbAdmin\Driver\Sql\AbstractDbProxy;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\SelectDto;

use function array_keys;
use function array_map;
use function implode;

abstract class AbstractQuery extends AbstractDbProxy implements QueryInterface
{
    /**
     * @inheritDoc
     */
    protected function addLimitClause(string $query, int $limit, int $offset = 0): string
    {
        return match(true) {
            $limit <= 0 => $query,
            $offset <= 0 => "$query LIMIT $limit",
            default => "$query LIMIT $limit OFFSET $offset",
        };
    }

    /**
     * @inheritDoc
     */
    public function getTableSelectQuery(SelectDto $select): string
    {
        return $this->addLimitClause($select->query(), $select->limit, $select->offset);
    }

    /**
     * Build SQL update or delete query with limit 1
     *
     * @param string $table
     * @param string $query Everything after UPDATE or DELETE
     * @param string $where
     *
     * @return string
     */
    abstract protected function limitToOne(string $table, string $query, string $where): string;

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
        $tableName = $this->_statement()->escapeTableName($table);
        $query = "UPDATE $tableName SET $assignments";

        return $limit <= 0 ? "$query $queryWhere" : $this->limitToOne($table, $query, $queryWhere);
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
        $tableName = $this->_statement()->escapeTableName($table);
        $query = "DELETE FROM $tableName";

        return $limit <= 0 ? "$query $queryWhere" : $this->limitToOne($table, $query, $queryWhere);
    }

    /**
     * @inheritDoc
     */
    public function convertColumn(ColumnDto $column): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function unconvertColumn(ColumnDto $column, string $value): string
    {
        return $value;
    }

    /**
     * Make SQL clause for update queries
     *
     * @param string $join
     * @param array $values
     * @param array $columns
     *
     * @return string
     */
    protected function getUpdateClause(string $join, array $values, array $columns): string
    {
        $updateClause = fn(string $value, string $column) => "$column = $value";

        return implode($join, array_map($updateClause, $values, $columns));
    }
}
