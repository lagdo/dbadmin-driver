<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Statement;

use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\SelectDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\UpsertDto;

interface QueryInterface
{
    /**
     * Select data from table
     *
     * @param SelectDto $input
     *
     * @return string
     */
    public function getTableSelectQuery(SelectDto $input): string;

    /**
     * Upsert multiple rows in a table
     *
     * @param UpsertDto $input
     *
     * @return array
     */
    public function getTableUpsertQueries(UpsertDto $input): array;

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
    public function getUpdateRowQuery(string $table, array $values, string $queryWhere, int $limit = 0): string;

    /**
     * Build a query to delete data from table
     *
     * @param string $table
     * @param string $queryWhere " WHERE ..."
     * @param int $limit 0 or 1
     *
     * @return string
     */
    public function getDeleteRowQuery(string $table, string $queryWhere, int $limit = 0): string;

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
