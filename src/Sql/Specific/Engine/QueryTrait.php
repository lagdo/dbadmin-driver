<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Engine;

use Lagdo\DbAdmin\Driver\Sql\Dto\TableDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableFieldDto;

trait QueryTrait
{
    /**
     * @return AbstractQuery
     */
    abstract protected function _query(): AbstractQuery;

    /**
     * Insert or update data in table
     *
     * @param string $table
     * @param array $rows
     * @param array $primary of arrays with escaped columns in keys and quoted data in values
     *
     * @return bool
     */
    // public function insertOrUpdate(string $table, array $rows, array $primary): bool
    // {
    //     return $this->_query()->insertOrUpdate($table, $rows, $primary);
    // }

    /**
     * Get last auto increment ID
     *
     * @return string
     */
    public function lastAutoIncrementId(): string
    {
        return $this->_query()->lastAutoIncrementId();
    }

    /**
     * Return query with a timeout
     *
     * @param string $query
     * @param int $timeout In seconds
     *
     * @return string or null if the driver doesn't support query timeouts
     */
    public function slowQuery(string $query, int $timeout): string|null
    {
        return $this->_query()->slowQuery($query, $timeout);
    }

    /**
     * Get approximate number of rows
     *
     * @param TableDto $tableStatus
     * @param array $where
     *
     * @return int|null
     */
    public function countRows(TableDto $tableStatus, array $where): int|null
    {
        return $this->_query()->countRows($tableStatus, $where);
    }

    /**
     * Convert column to be searchable
     *
     * @param string $idf Escaped column name
     * @param array $value ["op" => , "val" => ]
     * @param TableFieldDto $field
     *
     * @return string
     */
    public function convertSearch(string $idf, array $value, TableFieldDto $field): string
    {
        return $this->_query()->convertSearch($idf, $value, $field);
    }

    /**
     * Get view SELECT
     *
     * @param string $name
     *
     * @return array
     */
    public function view(string $name): array
    {
        return $this->_query()->view($name);
    }
}
