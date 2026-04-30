<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

class PartitionDto
{
    /**
     * @var array
     */
    public array $names = [];

    /**
     * @var array
     */
    public array $values = [];

    /**
     * The constructor
     *
     * @param string $strategy
     * @param string $columns
     * @param string $partitions
     */
    public function __construct(public string $strategy, public string $columns,
        public string $partitions = '')
    {}
}
