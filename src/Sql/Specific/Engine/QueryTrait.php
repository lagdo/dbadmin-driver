<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Engine;

use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\SelectFilterDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableDto;

trait QueryTrait
{
    /**
     * @return AbstractQuery
     */
    abstract protected function _query(): AbstractQuery;

    /**
     * Get last auto increment ID
     *
     * @return string
     */
    public function lastAutoIncrementId(): string
    {
        return $this->_query()->lastAutoIncrementId();
    }

    /**
     * Return query with a timeout
     *
     * @param string $query
     * @param int $timeout In seconds
     *
     * @return string or null if the driver doesn't support query timeouts
     */
    public function slowQuery(string $query, int $timeout): string|null
    {
        return $this->_query()->slowQuery($query, $timeout);
    }

    /**
     * Get approximate number of rows
     *
     * @param TableDto $tableStatus
     * @param array $where
     *
     * @return int|null
     */
    public function countRows(TableDto $tableStatus, array $where): int|null
    {
        return $this->_query()->countRows($tableStatus, $where);
    }

    /**
     * Convert column to be searchable
     *
     * @param SelectFilterDto $filter
     * @param ColumnDto $column
     *
     * @return string
     */
    public function convertSearch(SelectFilterDto $filter, ColumnDto $column): string
    {
        return $this->_query()->convertSearch($filter, $column);
    }

    /**
     * Get view SELECT
     *
     * @param string $name
     *
     * @return array
     */
    public function view(string $name): array
    {
        return $this->_query()->view($name);
    }
}
