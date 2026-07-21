<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

class ForeignKeyDdDto
{
    /**
     * @var ColumnAction
     */
    public ColumnAction $action;

    /**
     * @var string
     */
    public string $name = '';

    /**
     * @var string
     */
    public string $source = '';

    /**
     * @var string
     */
    public string $table = '';

    /**
     * @var string
     */
    public string $column = '';

    /**
     * @var string
     */
    public string $onUpdate = '';

    /**
     * @var string
     */
    public string $onDelete = '';

    /**
     * @var ForeignKeyDto|null
     */
    public ForeignKeyDto|null $foreignKey;

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
}
