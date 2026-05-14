<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

class TableAlterDto extends TableDdDto
{
    /**
     * @var TableDto
     */
    public TableDto $status;

    /**
     * @return array<ColumnInputDto>
     */
    public function editedColumns(): array
    {
        return $this->columns[ColumnAction::EDIT->value];
    }

    /**
     * @return array<string>
     */
    public function droppedColumns(): array
    {
        return $this->columns[ColumnAction::DROP->value];
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
}
