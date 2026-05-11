<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

use Closure;

use function implode;

class TableCreateDto extends TableDdlDto
{
    /**
     * Columns to add.
     *
     * @var array<string, array<ColumnInputDto>>
     */
    public array $columns = [];

    /**
     * @var string|null
     */
    public ?string $error = null;

    /**
     * @param Closure $quote
     *
     * @return string
     */
    public function options(Closure $quote): string
    {
        $options = [];
        if ($this->setComment && $this->comment !== null) {
            $options[] = 'COMMENT=' . $quote($this->comment);
        }
        if ($this->engine !== '') {
            $options[] = 'ENGINE=' . $quote($this->engine);
        }
        if ($this->collation !== '') {
            $options[] = 'COLLATE ' . $quote($this->collation);
        }
        if ($this->hasAutoIncrement && $this->autoIncrement !== 0) {
            $options[] = "AUTO_INCREMENT=$this->autoIncrement";
        }

        return implode(' ', $options);
    }

    /**
     * @return void
     */
    public function clearColumns(): void
    {
        $this->columns = [];
    }

    /**
     * @return array<ColumnInputDto>
     */
    public function addedColumns(): array
    {
        return $this->columns[ColumnAction::ADD->value];
    }
}
