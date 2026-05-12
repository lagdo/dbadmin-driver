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
    public string $oid = '';

    /**
     * @var string
     */
    public string $schema = '';

    /**
     * @var string
     */
    public string $collation = '';

    /**
     * @var bool
     */
    public bool $hasAutoIncrement = false;

    /**
     * @var int
     */
    public int $autoIncrementValue = 0;

    /**
     * @var string
     */
    public string $autoIncrementColumn = '';

    /**
     * @var int|null
     */
    public int|null $dataLength = null;

    /**
     * @var int|null
     */
    public int|null $dataFree = null;

    /**
     * @var int|null
     */
    public int|null $indexLength = null;

    /**
     * @var int|null
     */
    public int|null $rowCount = null;

    /**
     * @var string|null
     */
    public string|null $comment = null;

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
