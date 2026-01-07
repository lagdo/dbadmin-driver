<?php

namespace Lagdo\DbAdmin\Driver\Entity;

use Closure;

use function implode;

abstract class AbstractTableEntity
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
    public $collation = '';

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
    public $comment = '';

    /**
     * @var string
     */
    public $partitioning = '';

    /**
     * @var array<ForeignKeyEntity>
     */
    public $foreignKeys = [];

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
        $this->autoIncrement = $properties['autoIncrement'] ?? 0;
        // $this->partitioning = $properties['partitioning'] ?? '';
    }
}
