<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

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
     * @return bool
     */
    public function nameChanged(): bool
    {
        return $this->name === $this->current->name;
    }

    /**
     * @return bool
     */
    public function engineChanged(): bool
    {
        return $this->engine === $this->current->engine;
    }

    /**
     * @return bool
     */
    public function collationChanged(): bool
    {
        return $this->collation === $this->current->collation;
    }

    /**
     * @return bool
     */
    public function hasAutoIncrement(): bool
    {
        return $this->autoIncrement > 0;
    }

    /**
     * @return bool
     */
    public function commentChanged(): bool
    {
        return $this->comment !== null;
    }
}
