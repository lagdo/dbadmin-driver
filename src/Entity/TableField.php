<?php

namespace Lagdo\DbAdmin\Driver\Entity;

class TableField
{
    /**
     * The field name
     *
     * @var string
     */
    public $name = '';

    /**
     * The field type
     *
     * @var string
     */
    public $type = '';

    /**
     * The field full type
     *
     * @var string
     */
    public $fullType = '';

    /**
     * The field sign
     *
     * @var string
     */
    public $unsigned = '';

    /**
     * The field length
     *
     * @var integer
     */
    public $length = 0;

    /**
     * If the field has a default value
     *
     * @var boolean
     */
    public $hasDefault = false;

    /**
     * The field default value
     *
     * @var mixed
     */
    public $default = null;

    /**
     * If the field is null
     *
     * @var boolean
     */
    public $null = false;

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
     * The field collation
     *
     * @var string
     */
    public $collation = '';

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
    public $types;

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
}
