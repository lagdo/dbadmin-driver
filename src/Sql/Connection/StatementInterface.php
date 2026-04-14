<?php

namespace Lagdo\DbAdmin\Driver\Sql\Connection;

use Lagdo\DbAdmin\Driver\Sql\Dto\StatementFieldDto;

interface StatementInterface
{
    /**
     * Get the number of rows returned by the query
     *
     * @return int
     */
    public function rowCount(): int;

    /**
     * Fetch the next row as an array with field position as keys
     *
     * @return array|null
     */
    public function fetchRow(): array|null;

    /**
     * Fetch the next row as an array with field name as keys
     *
     * @return array|null
     */
    public function fetchAssoc(): array|null;

    /**
     * Fetch the next field
     *
     * @return StatementFieldDto|null
     */
    public function fetchField(): StatementFieldDto|null;
}
