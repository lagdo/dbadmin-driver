<?php

namespace Lagdo\DbAdmin\Driver\Dto;

class PartitionDto
{
    /**
     * The constructor
     *
     * @param string $strategy
     * @param string $fields
     */
    public function __construct(public string $strategy, public string $fields)
    {}
}
