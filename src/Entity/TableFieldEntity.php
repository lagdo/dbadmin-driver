<?php

namespace Lagdo\DbAdmin\Driver\Entity;

use function stripos;

class TableFieldEntity extends FieldType
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
    public $autoIncrement = false;

    /**
     * The action on update
     *
     * @var string
     */
    public $onUpdate = '';

    /**
     * The action on delete
     *
     * @var string
     */
    public $onDelete = '';

    /**
     * The field privileges
     *
     * @var array
     */
    public $privileges = [];

    /**
     * The field comment
     *
     * @var string
     */
    public $comment = '';

    /**
     * If the field is primary key
     *
     * @var boolean
     */
    public $primary = false;

    /**
     * If the field is generated
     *
     * @var boolean
     */
    public $generated = false;

    /**
     * The field types
     *
     * @var array
     */
    public $types = [];

    /**
     * If the field length is required
     *
     * @var boolean
     */
    public $lengthRequired = false;

    /**
     * If the field collation is hidden
     *
     * @var boolean
     */
    public $collationHidden = true;

    /**
     * If the field sign id idden
     *
     * @var boolean
     */
    public $unsignedHidden = false;

    /**
     * If the field on update trigger is hidden
     *
     * @var boolean
     */
    public $onUpdateHidden = true;

    /**
     * If the field on delete trigger is hidden
     *
     * @var boolean
     */
    public $onDeleteHidden = true;

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
