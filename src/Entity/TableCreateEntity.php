<?php

namespace Lagdo\DbAdmin\Driver\Entity;

class TableCreateEntity extends AbstractTableEntity
{
    /**
     * Columns to add.
     *
     * @var array<ColumnEntity>
     */
    public $columns = [];

    /**
     * @var string|null
     */
    public $error = null;

    /**
     * @return void
     */
    public function clearColumns(): void
    {
        $this->columns = [];
    }
}
