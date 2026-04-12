<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

class TableCreateDto extends AbstractTableDto
{
    /**
     * Columns to add.
     *
     * @var array<ColumnDto>
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
