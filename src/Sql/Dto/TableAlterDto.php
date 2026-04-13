<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

class TableAlterDto extends AbstractTableDto
{
    /**
     * @var TableDto
     */
    public ?TableDto $current = null;

    /**
     * Columns to add.
     *
     * @var array<ColumnDto>
     */
    public array $addedColumns = [];

    /**
     * Columns to change.
     *
     * @var array<ColumnDto>
     */
    public array $changedColumns = [];

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
        $this->addedColumns = [];
        $this->changedColumns = [];
        $this->droppedColumns = [];
    }
}
