<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Statement;

use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\SelectInputDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\UpsertDto;

interface QueryInterface
{
    /**
     * Select data from table
     *
     * @param SelectInputDto $input
     *
     * @return string
     */
    public function getTableSelectQuery(SelectInputDto $input): string;

    /**
     * Upsert multiple rows in a table
     *
     * @param UpsertDto $input
     *
     * @return array
     */
    public function getTableUpsertQueries(UpsertDto $input): array;

    /**
     * Convert column in select and edit
     *
     * @param ColumnDto $column one element from $this->columns()
     *
     * @return string
     */
    public function convertColumn(ColumnDto $column): string;

    /**
     * Convert value in edit after applying functions back
     *
     * @param ColumnDto $column One element from $this->columns()
     * @param string $value
     *
     * @return string
     */
    public function unconvertColumn(ColumnDto $column, string $value): string;
}
