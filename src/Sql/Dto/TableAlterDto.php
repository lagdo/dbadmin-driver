<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

class TableAlterDto extends AbstractTableDto
{
    /**
     * @var TableDto
     */
    public ?TableDto $current = null;

    /**
     * Columns to add or edit.
     *
     * @var array<string, array<ColumnInputDto>>
     */
    public array $inputs = [];

    /**
     * Columns to drop.
     *
     * @var array<string>
     */
    public array $droppedColumns = [];

    /**
     * @var string|null
     */
    public ?string $error = null;

    /**
     * @return void
     */
    public function clearColumns(): void
    {
        $this->inputs = [];
        $this->droppedColumns = [];
    }
}
