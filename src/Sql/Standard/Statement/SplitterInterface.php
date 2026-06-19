<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Statement;

use Lagdo\DbAdmin\Driver\Sql\Dto\QueryStreamDto;
use Generator;

interface SplitterInterface
{
    /**
     * Split a string or a file containing SQL queries.
     *
     * @param QueryStreamDto $input
     *
     * @return Generator
     */
    public function splitQueries(QueryStreamDto $input): Generator;
}
