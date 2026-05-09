<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

use Closure;

use function implode;

class TableAlterDto extends TableDdlDto
{
    /**
     * @var TableDto
     */
    public TableDto $current;

    /**
     * Columns to add, edit or drop.
     *
     * @var array<string, array<string|ColumnInputDto>>
     */
    public array $columns = [];

    /**
     * @var string|null
     */
    public string|null $error = null;

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
        if ($this->engine !== $this->current->engine) {
            $options[] = 'ENGINE=' . $quote($this->engine);
        }
        if ($this->collation !== $this->current->collation) {
            $options[] = 'COLLATE ' . $quote($this->collation);
        }
        if ($this->hasAutoIncrement && $this->autoIncrement !== 0) {
            $options[] = "AUTO_INCREMENT={$this->autoIncrement}";
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
}
