<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Statement;

use Lagdo\DbAdmin\Driver\Sql\Dto\TableFieldDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableSelectDto;

interface QueryInterface
{
    /**
     * Select data from table
     *
     * @param TableSelectDto $select
     *
     * @return string
     */
    public function getTableSelectQuery(TableSelectDto $select): string;

    /**
     * Convert field in select and edit
     *
     * @param TableFieldDto $field one element from $this->fields()
     *
     * @return string
     */
    public function convertField(TableFieldDto $field): string;

    /**
     * Convert value in edit after applying functions back
     *
     * @param TableFieldDto $field One element from $this->fields()
     * @param string $value
     *
     * @return string
     */
    public function unconvertField(TableFieldDto $field, string $value): string;
}
