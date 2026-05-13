<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Engine;

use Lagdo\DbAdmin\Driver\Sql\DbProxyTrait;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableDto;

trait TableTrait
{
    use DbProxyTrait;

    /**
     * Get status of a single table and fall back to name on error
     *
     * @param string $table
     * @param bool $fast Return only "Name", "Engine" and "Comment" columns
     *
     * @return TableDto
     */
    public function tableStatusOrName(string $table, bool $fast = false): TableDto
    {
        $status = $this->_engine()->tableStatus($table, $fast);
        return $status ?? new TableDto($table, $this->_engine()->columns(...));
    }
}
