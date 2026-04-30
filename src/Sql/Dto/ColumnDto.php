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
     * The column types
     *
     * @var array
     */
    public array $types = [];

    /**
     * If the column length is required
     *
     * @var boolean
     */
    public bool $lengthRequired = false;

    /**
     * If the column collation is hidden
     *
     * @var boolean
     */
    public bool $collationHidden = true;

    /**
     * If the column sign id hidden
     *
     * @var boolean
     */
    public bool $unsignedHidden = false;

    /**
     * If the column on update trigger is hidden
     *
     * @var boolean
     */
    public bool $onUpdateHidden = true;

    /**
     * If the column on delete trigger is hidden
     *
     * @var boolean
     */
    public bool $onDeleteHidden = true;

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
