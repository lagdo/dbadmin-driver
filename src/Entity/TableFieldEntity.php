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
     * The entity attributes
     *
     * @var array
     */
    private static $attrs = ['name', 'type', 'fullType', 'primary', 'nullable',
        'length', 'default', 'unsigned', 'autoIncrement', 'generated', 'collation',
        'lengthRequired', 'comment', 'collationHidden', 'unsignedHidden',
        'onUpdateHidden', 'onDeleteHidden', 'onUpdate', 'onDelete'];

    /**
     * The entity attributes
     *
     * @var array
     */
    private static $fields = ['name', 'type', 'primary', 'autoIncrement', 'unsigned',
        'length', 'comment', 'collation', 'generated', 'lengthRequired', 'onUpdate',
        'onDelete', 'collationHidden', 'unsignedHidden', 'onUpdateHidden', 'onDeleteHidden'];

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

    /**
     * Create an entity from database data
     *
     * @param array $field
     *
     * @return TableFieldEntity
     */
    public static function make(array $field): self
    {
        $entity = new static();
        foreach (self::$attrs as $attr) {
            // Attributes are set only when present.
            if (isset($field[$attr])) {
                $entity->$attr = $field[$attr];
            }
        }
        $entity->nullable = isset($field['nullable']);
        return $entity;
    }

    /**
     * Create an entity from js app data
     *
     * @param array $field
     *
     * @return TableFieldEntity
     */
    public static function fromArray(array $field): self
    {
        $entity = new static();
        foreach (self::$attrs as $attr) {
            $entity->$attr = $field[$attr];
        }
        return $entity;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [];
        foreach (self::$attrs as $attr) {
            $array[$attr] = $this->$attr;
        }
        return $array;
    }

    /**
     * @param array $values
     *
     * @return TableFieldEntity
     */
    public function update(array $values): self
    {
        foreach (self::$fields as $attr) {
            isset($values[$attr]) && $this->$attr = $values[$attr];
        }
        return $this;
    }

    /**
     * @param array $values
     *
     * @return bool
     */
    public function edited(array $values): bool
    {
        foreach (self::$fields as $attr) {
            if (isset($values[$attr]) && $this->$attr != $values[$attr]) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the values in two fields are equals
     *
     * @param TableFieldEntity $field
     *
     * @return bool
     */
    public function equals(TableFieldEntity $field): bool
    {
        foreach (self::$fields as $attr) {
            if ($field->$attr != $this->$attr) {
                return false;
            }
        }
        return true;
    }
}
