<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

/**
 * Inputs for a table column.
 */
class ColumnInputDto extends ColumnDto
{
    /**
     * Not yet implemented.
     *
     * @var string
     */
    public string $after = '';

    /**
     * @param ColumnDto $column
     * @param ColumnDto|null $typeColumn
     */
    public function __construct(public readonly ColumnDto $column,
        public readonly ColumnDto|null $typeColumn)
    {}
}
