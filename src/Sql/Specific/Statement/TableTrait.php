<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Statement;

use Lagdo\DbAdmin\Driver\Sql\Dto\TableAlterDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableCreateDto;

trait TableTrait
{
    /**
     * @return AbstractTable
     */
    abstract protected function _table(): AbstractTable;

    /**
     * Get SQL commands to create a table
     *
     * @param TableCreateDto $table
     *
     * @return array<string>
     */
    public function getCreateTableQueries(TableCreateDto $table): array
    {
        return $this->_table()->getCreateTableQueries($table);
    }

    /**
     * Get SQL commands to alter a table
     *
     * @param TableAlterDto $table
     *
     * @return array<string>
     */
    public function getAlterTableQueries(TableAlterDto $table): array
    {
        return $this->_table()->getAlterTableQueries($table);
    }
}
