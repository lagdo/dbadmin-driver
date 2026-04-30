<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

class TableCreateDto extends AbstractTableDto
{
    /**
     * Columns to add.
     *
     * @var array<string, array<ColumnInputDto>>
     */
    public array $inputs = [];

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
    }
}
