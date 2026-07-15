<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

class TableCreateDto extends TableDdDto
{
    /**
     * @return string
     */
    public function statusName(): string
    {
        return '';
    }

    /**
     * @return array<ColumnDto>
     */
    public function statusColumns(): array
    {
        return [];
    }

    /**
     * @return bool
     */
    public function engineChanged(): bool
    {
        return $this->engine !== '';
    }

    /**
     * @return bool
     */
    public function collationChanged(): bool
    {
        return $this->collation !== '';
    }

    /**
     * @return bool
     */
    public function primaryKeyChanged(): bool
    {
        foreach ($this->addedColumns() as $column) {
            if ($column->primary) {
                return true;
            }
        }
        return false;
    }
}
