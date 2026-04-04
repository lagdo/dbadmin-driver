<?php

namespace Lagdo\DbAdmin\Support\Dto;

class TableAlterDto extends AbstractTableDto
{
    /**
     * @var TableDto
     */
    public $current = null;

    /**
     * Columns to add.
     *
     * @var array<ColumnDto>
     */
    public $addedColumns = [];

    /**
     * Columns to change.
     *
     * @var array<ColumnDto>
     */
    public $changedColumns = [];

    /**
     * Columns to drop.
     *
     * @var array<string>
     */
    public $droppedColumns = [];

    /**
     * @var string|null
     */
    public $error = null;

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
