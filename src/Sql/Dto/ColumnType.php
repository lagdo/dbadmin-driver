<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

class ColumnType
{
    /**
     * The constructor
     *
     * @param string $name
     * @param string $type
     * @param string $fullType
     * @param string $unsigned
     * @param bool $nullable
     * @param string $collation
     * @param string $length
     * @param string $inout
     */
    public function __construct(public string $name = '', public string $type = '',
        public string $fullType = '', public string $unsigned = '', public bool $nullable = false,
        public string $collation = '', public string $length = '', public string $inout = '')
    {}
}
