<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

use function preg_match;

/**
 * Inputs for a table column.
 */
class ColumnInputDto extends ColumnDto
{
    /**
     * Not yet implemented.
     *
     * @var string
     */
    public string $after = '';

    /**
     * @param ColumnDto $column
     * @param ColumnDto|null $typeColumn
     */
    public function __construct(public readonly ColumnDto $column,
        public readonly ColumnDto|null $typeColumn)
    {}

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
    public function autoIncrementDisabled(): bool
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
