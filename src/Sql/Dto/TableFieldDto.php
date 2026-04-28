<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

use function stripos;

class TableFieldDto extends FieldType
{
    /**
     * The field default value
     *
     * @var mixed
     */
    public $default = null;

    /**
     * If the field is auto increment
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
     * The field privileges
     *
     * @var array
     */
    public array $privileges = [];

    /**
     * The field comment
     *
     * @var string|null
     */
    public string|null $comment = null;

    /**
     * If the field is primary key
     *
     * @var boolean
     */
    public bool $primary = false;

    /**
     * How the field is generated
     *
     * @var string
     */
    public string $generated = '';

    /**
     * The field types
     *
     * @var array
     */
    public array $types = [];

    /**
     * If the field length is required
     *
     * @var boolean
     */
    public bool $lengthRequired = false;

    /**
     * If the field collation is hidden
     *
     * @var boolean
     */
    public bool $collationHidden = true;

    /**
     * If the field sign id idden
     *
     * @var boolean
     */
    public bool $unsignedHidden = false;

    /**
     * If the field on update trigger is hidden
     *
     * @var boolean
     */
    public bool $onUpdateHidden = true;

    /**
     * If the field on delete trigger is hidden
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
