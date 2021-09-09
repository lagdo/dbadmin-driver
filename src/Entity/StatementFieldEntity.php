<?php

namespace Lagdo\DbAdmin\Driver\Entity;

class StatementFieldEntity
{
    /**
     * The field type
     *
     * @var string
     */
    protected $type = '';

    /**
     * If the field is binary
     *
     * @var bool
     */
    protected $isBinary = false;

    /**
     * The field name
     *
     * @var string
     */
    protected $name = '';

    /**
     * The field org name
     *
     * @var string
     */
    protected $orgName = '';

    /**
     * The field table name
     *
     * @var string
     */
    protected $table = '';

    /**
     * The field org table name
     *
     * @var string
     */
    protected $orgTable = '';

    /**
     * The constructor
     *
     * @param string $type
     * @param boolean $isBinary
     * @param string $name
     * @param string $orgName
     * @param string $table
     * @param string $orgTable
     */
    public function __construct(string $type, bool $isBinary,
        string $name, string $orgName, string $table = '', string $orgTable = '')
    {
        $this->type = $type;
        $this->isBinary = $isBinary;
        $this->name = $name;
        $this->orgName = $orgName;
        $this->table = $table;
        $this->orgTable = $orgTable;
    }

    public function type()
    {
        return $this->type;
    }

    public function isBinary()
    {
        return $this->isBinary;
    }

    public function name()
    {
        return $this->name;
    }

    public function orgName()
    {
        return $this->orgName;
    }

    public function table()
    {
        return $this->table;
    }

    public function orgTable()
    {
        return $this->orgTable;
    }

    public function tableName()
    {
        return $this->table ?? $this->orgTable;
    }
}
