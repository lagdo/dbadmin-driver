<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Driver;

use Lagdo\DbAdmin\Support\Dto\TableDto;

trait TableTrait
{
    /**
     * @var TableInterface
     */
    private TableInterface $table;

    /**
     * @return TableInterface
     */
    private function _t(): TableInterface
    {
        return $this->table ??= new Table($this, $this->grammar(), $this->utils);
    }

    /**
     * Get status of a single table and fall back to name on error
     *
     * @param string $table
     * @param bool $fast Return only "Name", "Engine" and "Comment" fields
     *
     * @return TableDto
     */
    public function tableStatusOrName(string $table, bool $fast = false): TableDto
    {
        return $this->_t()->tableStatusOrName($table, $fast);
    }
}
