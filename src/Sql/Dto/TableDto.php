<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

class TableDto
{
    /**
     * @var string
     */
    public string $name = '';

    /**
     * @var string
     */
    public string $engine = '';

    /**
     * @var string
     */
    public string $schema = '';

    /**
     * @var string
     */
    public string $collation = '';

    /**
     * @var integer
     */
    public int $dataLength = 0;

    /**
     * @var integer
     */
    public int $indexLength = 0;

    /**
     * @var string
     */
    public string $comment = '';

    /**
     * @var string
     */
    public string $oid = '';

    /**
     * @var int
     */
    public int $rowCount = 0;

    /**
     * @var bool
     */
    public bool $hasAutoIncrement = false;

    /**
     * @var integer
     */
    public int $autoIncrement = 0;

    /**
     * @var string
     */
    public string $partitioning = '';

    /**
     * @param string $name The table name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }
}
