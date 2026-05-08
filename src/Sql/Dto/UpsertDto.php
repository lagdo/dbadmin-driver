<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

use function array_values;
use function count;

class UpsertDto
{
    /**
     * @param string $table
     * @param array $keys
     * @param array $values
     */
    public function __construct(public string $table, public array $keys, public array $values)
    {}

    /**
     * @return int
     */
    public function keyCount(): int
    {
        return count($this->keys);
    }

    /**
     * @return array
     */
    public function rows(): array
    {
        return [...array_values($this->keys), ...array_values($this->values)];
    }
}
