<?php

namespace Lagdo\DbAdmin\Driver\Entity;

class TableAlterEntity extends AbstractTableEntity
{
    /**
     * @var TableEntity
     */
    public $current = null;

    /**
     * Columns to add.
     *
     * @var array<ColumnEntity>
     */
    public $addedColumns = [];

    /**
     * Columns to change.
     *
     * @var array<ColumnEntity>
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
