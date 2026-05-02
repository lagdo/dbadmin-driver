<?php

namespace Lagdo\DbAdmin\Driver\Sql\Connection;

use Lagdo\DbAdmin\Driver\Sql\Dto\ResultColumnDto;

interface QueryResultInterface
{
    /**
     * Check if the query returned an error
     *
     * @return bool
     */
    public function hasError(): bool;

    /**
     * Check if the query returned rows
     *
     * @return bool
     */
    public function hasRowset(): bool;

    /**
     * Get the number of rows returned by the query
     *
     * @return int
     */
    public function rowCount(): int;

    /**
     * Fetch the next row as an array with column position as keys
     *
     * @return array|null
     */
    public function fetchRow(): array|null;

    /**
     * Fetch the next row as an array with column name as keys
     *
     * @return array|null
     */
    public function fetchAssoc(): array|null;

    /**
     * Fetch the next column
     *
     * @return ResultColumnDto|null
     */
    public function fetchColumn(): ResultColumnDto|null;
}
