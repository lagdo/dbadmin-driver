<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

use function preg_match;

/**
 * Inputs for a table column.
 */
class ColumnDdDto extends ColumnDto
{
    /**
     * Not yet implemented.
     *
     * @var string
     */
    public string $after = '';

    /**
     * @var ColumnDto|null
     */
    public ColumnDto|null $typeColumn;

    /**
     * @param ColumnDto $column
     * @param ColumnAction $action
     */
    public function __construct(public readonly ColumnDto $column,
        public readonly ColumnAction $action)
    {}

    /**
     * @return bool
     */
    public function unchanged(): bool
    {
        return $this->action === ColumnAction::NONE;
    }

    /**
     * @return bool
     */
    public function added(): bool
    {
        return $this->action === ColumnAction::ADD;
    }

    /**
     * @return bool
     */
    public function edited(): bool
    {
        return $this->action === ColumnAction::EDIT;
    }

    /**
     * @return bool
     */
    public function dropped(): bool
    {
        return $this->action === ColumnAction::DROP;
    }

    /**
     * @return string
     */
    public function statusName(): string
    {
        return $this->column->name;
    }

    /**
     * @return bool
     */
    public function nameChanged(): bool
    {
        return $this->name !== $this->column->name;
    }

    /**
     * @return bool
     */
    public function primaryChanged(): bool
    {
        return $this->primary !== $this->column->primary;
    }

    /**
     * @return bool
     */
    public function nullableChanged(): bool
    {
        return $this->nullable !== $this->column->nullable;
    }

    /**
     * @return bool
     */
    public function autoIncrementChanged(): bool
    {
        return $this->autoIncrement !== $this->column->autoIncrement;
    }

    /**
     * @return bool
     */
    public function autoIncrementRemoved(): bool
    {
        return $this->autoIncrementChanged() &&
            $this->column->name !== '' && !$this->autoIncrement;
    }

    /**
     * @return bool
     */
    public function defaultChanged(): bool
    {
        return $this->default !== $this->column->default;
    }

    /**
     * @return bool
     */
    public function typeChanged(): bool
    {
        return $this->type !== $this->column->type ||
            $this->length !== $this->column->length ||
            $this->unsigned !== $this->column->unsigned ||
            $this->collation !== $this->column->collation;
    }

    /**
     * @return bool
     */
    public function onUpdateChanged(): bool
    {
        $typeColumn = $this->typeColumn ?? $this;
        return $this->onUpdate !== $this->column->onUpdate &&
            preg_match('~timestamp|datetime~', $typeColumn->type);
    }

    /**
     * @return bool
     */
    public function hasComment(): bool
    {
        return $this->comment !== null;
    }
}
