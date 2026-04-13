<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

use Closure;

use function implode;

abstract class AbstractTableDto
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
     * @var string
     */
    public string $comment = '';

    /**
     * @var string
     */
    public string $partitioning = '';

    /**
     * @var array<ForeignKeyDto>
     */
    public array $foreignKeys = [];

    /**
     * @param array $properties
     */
    public function __construct(array $properties = [])
    {
        $this->properties($properties);
    }

    /**
     * @param Closure $quote
     *
     * @return string
     */
    public function options(Closure $quote): string
    {
        $options = [];
        if ($this->comment) {
            $options[] = 'COMMENT=' . $quote($this->comment);
        }
        if ($this->engine) {
            $options[] = 'ENGINE=' . $quote($this->engine);
        }
        if ($this->collation) {
            $options[] = 'COLLATE ' . $quote($this->collation);
        }
        if ($this->autoIncrement !== 0) {
            $options[] = "AUTO_INCREMENT=$this->autoIncrement";
        }
        return implode(' ', $options);
    }

    /**
     * @param array $properties
     *
     * @return void
     */
    public function properties(array $properties): void
    {
        $this->name = $properties['name'] ?? '';
        $this->engine = $properties['engine'] ?? '';
        $this->collation = $properties['collation'] ?? '';
        $this->comment = $properties['comment'] ?? '';
        $this->hasAutoIncrement = $properties['hasAutoIncrement'] ?? false;
        if ($this->hasAutoIncrement) {
            $this->autoIncrement = (int)($properties['autoIncrement'] ?? 0);
        }
        // $this->partitioning = $properties['partitioning'] ?? '';
    }
}
