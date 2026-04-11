<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Driver;

use Lagdo\DbAdmin\Support\Dto\TableDto;

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
