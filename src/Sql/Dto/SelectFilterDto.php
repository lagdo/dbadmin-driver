<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

use function in_array;

class SelectFilterDto
{
    /**
     * @var array<ColumnDto>
     */
    public array $columns;

    /**
     * @param string $columnName
     * @param string $operator
     * @param string $operand
     */
    public function __construct(public string $columnName,
        public string $operator, public string $operand)
    {}

    /**
     * @param array $operators
     *
     * @return bool
     */
    public function isValid(array $operators): bool
    {
        return ($this->columnName !== '' || $this->operand !== '') &&
            in_array($this->operator, $operators);
    }
}
