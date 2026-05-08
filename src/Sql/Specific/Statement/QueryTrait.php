<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Statement;

use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\SelectInputDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\UpsertDto;

trait QueryTrait
{
    /**
     * @return AbstractQuery
     */
    abstract protected function _query(): AbstractQuery;

    /**
     * Build SQL update or delete query with limit 1
     *
     * @param string $table
     * @param string $query Everything after UPDATE or DELETE
     * @param string $where
     *
     * @return string
     */
    public function limitToOne(string $table, string $query, string $where): string
    {
        return $this->_query()->limitToOne($table, $query, $where);
    }

    /**
     * Select data from table
     *
     * @param SelectInputDto $input
     *
     * @return string
     */
    public function getTableSelectQuery(SelectInputDto $input): string
    {
        return $this->_query()->getTableSelectQuery($input);
    }

    /**
     * Upsert multiple rows in a table
     *
     * @param UpsertDto $input
     *
     * @return array
     */
    public function getTableUpsertQueries(UpsertDto $input): array
    {
        return $this->_query()->getTableUpsertQueries($input);
    }

    /**
     * Convert column in select and edit
     *
     * @param ColumnDto $column one element from $this->columns()
     *
     * @return string
     */
    public function convertColumn(ColumnDto $column): string
    {
        return $this->_query()->convertColumn($column);
    }

    /**
     * Convert value in edit after applying functions back
     *
     * @param ColumnDto $column One element from $this->columns()
     * @param string $value
     *
     * @return string
     */
    public function unconvertColumn(ColumnDto $column, string $value): string
    {
        return $this->_query()->unconvertColumn($column, $value);
    }
}
