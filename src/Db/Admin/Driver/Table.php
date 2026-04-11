<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Driver;

use Lagdo\DbAdmin\Support\Db\AbstractDelegate;
use Lagdo\DbAdmin\Support\Dto\TableDto;

class Table extends AbstractDelegate implements TableInterface
{
    /**
     * @inheritDoc
     */
    public function tableStatusOrName(string $table, bool $fast = false): TableDto
    {
        if (($status = $this->driver->tableStatus($table, $fast))) {
            return $status;
        }
        return new TableDto($table);
    }
}
