<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Statement;

use Lagdo\DbAdmin\Driver\Sql\Dto\TableFieldDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableSelectDto;

trait QueryTrait
{
    /**
     * @return AbstractQuery
     */
    abstract protected function _query(): AbstractQuery;

    /**
     * Build SQL update or delete query with limit 1
     *
     * @param string $table
     * @param string $query Everything after UPDATE or DELETE
     * @param string $where
     *
     * @return string
     */
    public function limitToOne(string $table, string $query, string $where): string
    {
        return $this->_query()->limitToOne($table, $query, $where);
    }

    /**
     * Select data from table
     *
     * @param TableSelectDto $select
     *
     * @return string
     */
    public function getTableSelectQuery(TableSelectDto $select): string
    {
        return $this->_query()->getTableSelectQuery($select);
    }

    /**
     * Convert field in select and edit
     *
     * @param TableFieldDto $field one element from $this->fields()
     *
     * @return string
     */
    public function convertField(TableFieldDto $field): string
    {
        return $this->_query()->convertField($field);
    }

    /**
     * Convert value in edit after applying functions back
     *
     * @param TableFieldDto $field One element from $this->fields()
     * @param string $value
     *
     * @return string
     */
    public function unconvertField(TableFieldDto $field, string $value): string
    {
        return $this->_query()->unconvertField($field, $value);
    }
}
