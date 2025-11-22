<?php

namespace Lagdo\DbAdmin\Driver\Entity;

class FieldType
{
    /**
     * The constructor
     *
     * @param string $name
     * @param string $type
     * @param string $fullType
     * @param string $unsigned
     * @param bool $null
     * @param string $collation
     * @param string $length
     * @param string $inout
     */
    public function __construct(public string $name = '', public string $type = '',
        public string $fullType = '', public string $unsigned = '', public bool $null = false,
        public string $collation = '', public string $length = '', public string $inout = '')
    {}
}
