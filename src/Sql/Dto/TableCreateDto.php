<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

class TableCreateDto extends AbstractTableDto
{
    /**
     * Columns to add.
     *
     * @var array<ColumnDto>
     */
    public array $columns = [];

    /**
     * @var string|null
     */
    public ?string $error = null;

    /**
     * @return void
     */
    public function clearColumns(): void
    {
        $this->columns = [];
    }
}
