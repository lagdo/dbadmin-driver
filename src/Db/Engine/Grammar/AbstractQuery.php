<?php

namespace Lagdo\DbAdmin\Support\Db\Engine\Grammar;

use Lagdo\DbAdmin\Support\Db\AbstractDbProxy;
use Lagdo\DbAdmin\Support\Dto\TableSelectDto;
use Lagdo\DbAdmin\Support\Dto\TableFieldDto;

use function implode;

abstract class AbstractQuery extends AbstractDbProxy implements QueryInterface
{
    /**
     * Build SQL update or delete query with limit 1
     *
     * @param string $table
     * @param string $query Everything after UPDATE or DELETE
     * @param string $where
     *
     * @return string
     */
    abstract public function limitToOne(string $table, string $query, string $where): string;

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
            ' FROM ' . $this->_grammar()->escapeTableName($select->table);
        $limit = +$select->limit;
        $offset = $select->page ? $limit * $select->page : 0;

        return 'SELECT' . $this->getLimitClause($query, $select->clauses, $limit, $offset);
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
}
