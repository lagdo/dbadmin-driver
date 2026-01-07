<?php

namespace Lagdo\DbAdmin\Driver\Entity;

class TableEntity
{
    /**
     * @var string
     */
    public $name = '';

    /**
     * @var string
     */
    public $engine = '';

    /**
     * @var string
     */
    public $schema = '';

    /**
     * @var string
     */
    public $collation = '';

    /**
     * @var integer
     */
    public $dataLength = 0;

    /**
     * @var integer
     */
    public $indexLength = 0;

    /**
     * @var string
     */
    public $comment = '';

    /**
     * @var string
     */
    public $oid = '';

    /**
     * @var array
     */
    public $rows = [];

    /**
     * Columns to add when creating or altering a table.
     *
     * @var array
     */
    public $fields = [];

    /**
     * Columns to edit when altering a table.
     *
     * @var array
     */
    public $edited = [];

    /**
     * Columns to drop when altering a table.
     *
     * @var array
     */
    public $dropped = [];

    /**
     * @var array
     */
    public $foreign = [];

    /**
     * @var bool
     */
    public $hasAutoIncrement = false;

    /**
     * @var integer
     */
    public $autoIncrement = 0;

    /**
     * @var string
     */
    public $partitioning = '';

    /**
     * The constructor
     *
     * @param string $name The table name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
