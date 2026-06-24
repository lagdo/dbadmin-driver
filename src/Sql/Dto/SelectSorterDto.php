<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

class SelectSorterDto
{
    /**
     * @var ColumnDto
     */
    public ColumnDto $column;

    /**
     * @param string $columnName
     * @param bool $desc
     */
    public function __construct(public string $columnName, public bool $desc)
    {}

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->columnName !== '';
    }
}
