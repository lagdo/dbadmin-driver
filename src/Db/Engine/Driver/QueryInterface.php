<?php

namespace Lagdo\DbAdmin\Support\Db\Engine\Driver;

use Lagdo\DbAdmin\Support\Dto\TableDto;
use Lagdo\DbAdmin\Support\Dto\TableFieldDto;

interface QueryInterface
{
    /**
     * Insert or update data in table
     *
     * @param string $table
     * @param array $rows
     * @param array $primary of arrays with escaped columns in keys and quoted data in values
     *
     * @return bool
     */
    // public function insertOrUpdate(string $table, array $rows, array $primary): bool;

    /**
     * Get last auto increment ID
     *
     * @return string
     */
    public function lastAutoIncrementId(): string;

    /**
     * Return query with a timeout
     *
     * @param string $query
     * @param int $timeout In seconds
     *
     * @return string|null
     */
    public function slowQuery(string $query, int $timeout): string|null;

    /**
     * Get approximate number of rows
     *
     * @param TableDto $tableStatus
     * @param array $where
     *
     * @return int|null
     */
    public function countRows(TableDto $tableStatus, array $where): int|null;

    /**
     * Convert column to be searchable
     *
     * @param string $idf escaped column name
     * @param array $value array("op" => , "val" => )
     * @param TableFieldDto $field
     *
     * @return string
     */
    public function convertSearch(string $idf, array $value, TableFieldDto $field): string;

    /**
     * Get view SELECT
     *
     * @param string $name
     *
     * @return array array("select" => )
     */
    public function view(string $name): array;
}
