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
     * @var array
     */
    public $fields = [];

    /**
     * Columns to drop when altering the table.
     *
     * @var array
     */
    public $drop = [];

    /**
     * @var array
     */
    public $foreign = [];

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
