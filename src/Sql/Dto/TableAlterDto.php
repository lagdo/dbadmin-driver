<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

class TableAlterDto extends TableDdDto
{
    /**
     * @var TableDto
     */
    public TableDto $status;

    /**
     * @return array<ColumnDdDto>
     */
    public function editedColumns(): array
    {
        return array_filter($this->columns, fn(ColumnDdDto $column) => $column->edited());
    }

    /**
     * @return array<ColumnDto>
     */
    public function droppedColumns(): array
    {
        return array_filter($this->columns, fn(ColumnDdDto $column) => $column->dropped());
    }

    /**
     * @return string
     */
    public function statusName(): string
    {
        return $this->status->name;
    }

    /**
     * @return array<ColumnDto>
     */
    public function statusColumns(): array
    {
        return $this->status->columns();
    }

    /**
     * @return bool
     */
    public function nameChanged(): bool
    {
        return $this->name !== $this->status->name;
    }

    /**
     * @return bool
     */
    public function engineChanged(): bool
    {
        return $this->engine !== $this->status->engine;
    }

    /**
     * @return bool
     */
    public function collationChanged(): bool
    {
        return $this->collation !== $this->status->collation;
    }

    /**
     * @return bool
     */
    public function primaryKeyChanged(): bool
    {
        foreach ($this->editedColumns() as $column) {
            if ($column->primaryChanged()) {
                return true;
            }
        }
        foreach ($this->addedColumns() as $column) {
            if ($column->primary) {
                return true;
            }
        }
        foreach ($this->droppedColumns() as $column) {
            if ($column->primary) {
                return true;
            }
        }
        return false;
    }
}
