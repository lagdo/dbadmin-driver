<?php

namespace Lagdo\DbAdmin\Driver\Entity;

class Table
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
     * The constructor
     *
     * @param string $name The table name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
