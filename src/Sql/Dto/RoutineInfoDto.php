<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

class RoutineInfoDto
{
    /**
     * @param string $definition
     * @param string $language
     * @param array<FieldType> $params
     * @param FieldType|null $return
     * @param string $comment
     */
    public function __construct(public string $definition, public string $language,
        public array $params, public FieldType|null $return = null, public string $comment = '')
    {}
}
