<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

use function stripos;

class ColumnDto extends ColumnType
{
    /**
     * The column default value
     *
     * @var mixed
     */
    public $default = null;

    /**
     * If the column is auto increment
     *
     * @var boolean
     */
    public bool $autoIncrement = false;

    /**
     * The action on update
     *
     * @var string
     */
    public string $onUpdate = '';

    /**
     * The action on delete
     *
     * @var string
     */
    public string $onDelete = '';

    /**
     * The column privileges
     *
     * @var array
     */
    public array $privileges = [];

    /**
     * The column comment
     *
     * @var string|null
     */
    public string|null $comment = null;

    /**
     * If the column is primary key
     *
     * @var boolean
     */
    public bool $primary = false;

    /**
     * How the column is generated
     *
     * @var string
     */
    public string $generated = '';

    /**
     * @return boolean
     */
    public function hasDefault(): bool
    {
        return $this->default !== null;
    }

    /**
     * @return boolean
     */
    public function isDisabled(): bool
    {
        return stripos($this->default ?? '', "GENERATED ALWAYS AS ") === 0;
    }
}
