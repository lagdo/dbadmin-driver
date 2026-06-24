<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

use function in_array;

class SelectColumnDto
{
    /**
     * @var ColumnDto|null
     */
    public ColumnDto|null $column;

    /**
     * @param string $columnName
     * @param string $func
     */
    public function __construct(public string $columnName, public string $func)
    {}

    /**
     * @param array $functions
     * @param array $grouping
     *
     * @return bool
     */
    public function isValid(array $functions, array $grouping): bool
    {
        return $this->func === 'count' ||
            ($this->columnName !== '' && (!$this->func ||
                in_array($this->func, $functions) ||
                in_array($this->func, $grouping)));
    }
}
