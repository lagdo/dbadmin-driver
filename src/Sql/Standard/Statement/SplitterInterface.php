<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Statement;

use Lagdo\DbAdmin\Driver\Sql\Dto\QueryCodeDto;
use Generator;

interface SplitterInterface
{
    /**
     * Split a string or a file containing SQL queries.
     *
     * @param QueryCodeDto $input
     *
     * @return Generator
     */
    public function splitQueries(QueryCodeDto $input): Generator;
}
