<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

use Closure;

abstract class TableDdlDto
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
    public string $collation = '';

    /**
     * @var bool
     */
    public bool $hasAutoIncrement = false;

    /**
     * @var integer
     */
    public int $autoIncrement = 0;

    /**
     * @var bool
     */
    public bool $setComment = false;

    /**
     * @var string|null
     */
    public string|null $comment = null;

    /**
     * @var string
     */
    public string $partitioning = '';

    /**
     * @var array<ForeignKeyDto>
     */
    public array $foreignKeys = [];

    /**
     * @var array<ColumnDto>
     */
    private array $referencableColumns;

    /**
     * @param array $inputs
     * @param Closure $getColumns
     */
    public function __construct(array $inputs, private Closure $getColumns)
    {
        $this->name = $inputs['name'] ?? '';
        $this->engine = $inputs['engine'] ?? '';
        $this->collation = $inputs['collation'] ?? '';
        $this->setComment = $inputs['setComment'] ?? false;
        $this->hasAutoIncrement = $inputs['hasAutoIncrement'] ?? false;
        if ($this->hasAutoIncrement) {
            $this->autoIncrement = (int)($inputs['autoIncrement'] ?? 0);
        }
        if ($this->setComment) {
            $this->comment = $inputs['comment'] ?? null;
        }
        // $this->partitioning = $inputs['partitioning'] ?? '';
    }

    /**
     * @return array<ColumnDto>
     */
    public function getReferencableColumns(): array
    {
        return $this->referencableColumns ??= ($this->getColumns)($this->name);
    }
}
