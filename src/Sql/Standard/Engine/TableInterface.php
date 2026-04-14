<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Engine;

use Lagdo\DbAdmin\Driver\Sql\Dto\TableDto;

interface TableInterface
{
    /**
     * Get status of a single table and fall back to name on error
     *
     * @param string $table
     * @param bool $fast Return only "Name", "Engine" and "Comment" fields
     *
     * @return TableDto
     */
    public function tableStatusOrName(string $table, bool $fast = false): TableDto;
}
