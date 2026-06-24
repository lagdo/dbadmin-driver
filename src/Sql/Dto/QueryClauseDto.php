<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

class QueryClauseDto
{
    /**
     * @param string $keyword
     * @param string $separator
     * @param array $clauses
     */
    public function __construct(public string $keyword,
        public string $separator, public array $clauses)
    {}
}
