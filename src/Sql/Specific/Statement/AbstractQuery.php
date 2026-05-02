<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Statement;

use Lagdo\DbAdmin\Driver\Sql\AbstractDbProxy;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\SelectInputDto;

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
    public function getTableSelectQuery(SelectInputDto $input): string
    {
        $query = implode(', ', $input->columns) .
            ' FROM ' . $this->_statement()->escapeTableName($input->table);
        $limit = +$input->limit;
        $offset = $input->page ? $limit * $input->page : 0;

        return 'SELECT' . $this->getLimitClause($query, $input->clauses, $limit, $offset);
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
}
