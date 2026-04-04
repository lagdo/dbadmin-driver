<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Grammar;

use Lagdo\DbAdmin\Support\Dto\TableFieldDto;
use Lagdo\DbAdmin\Support\Dto\TableSelectDto;

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
     * Get query to compute number of found rows
     *
     * @param string $table
     * @param array $where
     * @param bool $isGroup
     * @param array $groups
     *
     * @return string
     */
    public function getRowCountQuery(string $table, array $where, bool $isGroup, array $groups): string;

    /**
     * Build a query to select data from table
     *
     * @param string $table
     * @param array $select Result of processSelectColumns()[0]
     * @param array $where Result of processSelectWhere()
     * @param array $group Result of processSelectColumns()[1]
     * @param array $order Result of processSelectOrder()
     * @param int $limit Result of processSelectLimit()
     * @param int $page Index of page starting at zero
     *
     * @return string
     */
    public function getRowSelectQuery(string $table, array $select, array $where, array $group = [],
        array $order = [], int $limit = 1, int $page = 0): string;

    /**
     * Build a query to insert data into table
     *
     * @param string $table
     * @param array $values Escaped columns in keys, quoted data in values
     *
     * @return string
     */
    public function getRowInsertQuery(string $table, array $values): string;

    /**
     * Build a query to update data in table
     *
     * @param string $table
     * @param array $values Escaped columns in keys, quoted data in values
     * @param string $queryWhere " WHERE ..."
     * @param int $limit 0 or 1
     *
     * @return string
     */
    public function getRowUpdateQuery(string $table, array $values, string $queryWhere, int $limit = 0): string;

    /**
     * Build a query to delete data from table
     *
     * @param string $table
     * @param string $queryWhere " WHERE ..."
     * @param int $limit 0 or 1
     *
     * @return string
     */
    public function getRowDeleteQuery(string $table, string $queryWhere, int $limit = 0): string;

    /**
     * Convert field in select and edit
     *
     * @param TableFieldDto $field one element from $this->fields()
     *
     * @return string
     */
    public function convertField(TableFieldDto $field): string;

    /**
     * Get select clause for convertible fields
     *
     * @param array $columns
     * @param array $fields
     * @param array $select
     *
     * @return string
     */
    public function convertFields(array $columns, array $fields, array $select = []): string;

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
