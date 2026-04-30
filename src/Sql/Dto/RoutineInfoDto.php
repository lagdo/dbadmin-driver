<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

class RoutineInfoDto
{
    /**
     * @param string $definition
     * @param string $language
     * @param array<ColumnType> $params
     * @param ColumnType|null $return
     * @param string $comment
     */
    public function __construct(public string $definition, public string $language,
        public array $params, public ColumnType|null $return = null, public string $comment = '')
    {}
}
