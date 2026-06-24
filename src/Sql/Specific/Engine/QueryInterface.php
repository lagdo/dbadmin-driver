<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Engine;

use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\SelectFilterDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableDto;

interface QueryInterface
{
    /**
     * Get last auto increment ID
     *
     * @return string
     */
    public function lastAutoIncrementId(): string;

    /**
     * Return query with a timeout
     *
     * @param string $query
     * @param int $timeout In seconds
     *
     * @return string|null
     */
    public function slowQuery(string $query, int $timeout): string|null;

    /**
     * Get approximate number of rows
     *
     * @param TableDto $tableStatus
     * @param array $where
     *
     * @return int|null
     */
    public function countRows(TableDto $tableStatus, array $where): int|null;

    /**
     * Convert column to be searchable
     *
     * @param SelectFilterDto $filter
     * @param ColumnDto $column
     *
     * @return string
     */
    public function convertSearch(SelectFilterDto $filter, ColumnDto $column): string;

    /**
     * Get view SELECT
     *
     * @param string $name
     *
     * @return array
     */
    public function view(string $name): array;
}
