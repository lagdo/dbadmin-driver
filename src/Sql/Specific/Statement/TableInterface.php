<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Statement;

use Lagdo\DbAdmin\Driver\Sql\Dto\TableAlterDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableCreateDto;

interface TableInterface
{
    /**
     * Get SQL commands to create a table
     *
     * @param TableCreateDto $table
     *
     * @return array<string>
     */
    public function getCreateTableQueries(TableCreateDto $table): array;

    /**
     * Get SQL commands to alter a table
     *
     * @param TableAlterDto $table
     *
     * @return array<string>
     */
    public function getAlterTableQueries(TableAlterDto $table): array;
}
