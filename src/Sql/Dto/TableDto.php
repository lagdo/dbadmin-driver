<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

use Closure;

class TableDto
{
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
     * @var array<ColumnDto>
     */
    private array $columns;

    /**
     * @param string $name The table name
     * @param Closure $columnsGetter
     */
    public function __construct(public string $name, private Closure $columnsGetter)
    {}

    /**
     * @return array<ColumnDto>
     */
    public function columns(): array
    {
        return $this->columns ??= ($this->columnsGetter)($this);
    }
}
