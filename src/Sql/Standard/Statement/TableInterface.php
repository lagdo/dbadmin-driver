<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Statement;

use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnInputDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnType;

interface TableInterface
{
    /**
     * Get default value clause
     *
     * @param ColumnDto $column
     *
     * @return string
     */
    public function getDefaultValueClause(ColumnDto $column): string;

    /**
     * Create SQL string from column type
     *
     * @param ColumnType $column
     *
     * @return string
     */
    public function getColumnType(ColumnType $column, string $collate = "COLLATE"): string;

    /**
     * Create SQL string from column
     * This is the process_field() function in Adminer.
     *
     * @param ColumnDto $column Basic column information
     * @param ColumnDto $typeColumn Information about column type
     *
     * @return ColumnInputDto
     */
    public function makeColumnInput(ColumnDto $column, ColumnDto $typeColumn): ColumnInputDto;
}
