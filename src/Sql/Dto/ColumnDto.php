<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

use function is_string;
use function stripos;

class ColumnDto extends ColumnType
{
    /**
     * The column default value
     *
     * @var mixed
     */
    public mixed $default = null;

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
     * @param bool $isString Also check if the default value is a string
     *
     * @return boolean
     */
    public function hasDefault(bool $isString = false): bool
    {
        return !$isString ? $this->default !== null :
            $this->default !== null && is_string($this->default);
    }

    /**
     * @return boolean
     */
    public function isDisabled(): bool
    {
        return stripos($this->default ?? '', "GENERATED ALWAYS AS ") === 0;
    }
}
