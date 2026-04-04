<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Grammar;

use Lagdo\DbAdmin\Support\Dto\TableFieldDto;
use Lagdo\DbAdmin\Support\Dto\TableSelectDto;

trait QueryTrait
{
    /**
     * @return QueryInterface
     */
    abstract protected function _query(): QueryInterface;

    /**
     * @inheritDoc
     */
    public function getTableSelectQuery(TableSelectDto $select): string
    {
        return $this->_query()->getTableSelectQuery($select);
    }

    /**
     * @inheritDoc
     */
    public function getRowCountQuery(string $table, array $where, bool $isGroup, array $groups): string
    {
        return $this->_query()->getRowCountQuery($table, $where, $isGroup, $groups);
    }

    /**
     * @inheritDoc
     */
    public function getRowSelectQuery(string $table, array $select, array $where, array $group = [],
        array $order = [], int $limit = 1, int $page = 0): string
    {
        return $this->_query()->getRowSelectQuery($table, $select, $where, $group, $order, $limit, $page);
    }

    /**
     * @inheritDoc
     */
    public function getRowInsertQuery(string $table, array $values): string
    {
        return $this->_query()->getRowInsertQuery($table, $values);
    }

    /**
     * @inheritDoc
     */
    public function getRowUpdateQuery(string $table, array $values, string $queryWhere, int $limit = 0): string
    {
        return $this->_query()->getRowUpdateQuery($table, $values, $queryWhere, $limit);
    }

    /**
     * @inheritDoc
     */
    public function getRowDeleteQuery(string $table, string $queryWhere, int $limit = 0): string
    {
        return $this->_query()->getRowDeleteQuery($table, $queryWhere, $limit = 0);
    }

    /**
     * @inheritDoc
     */
    public function convertField(TableFieldDto $field): string
    {
        return $this->_query()->convertField($field);
    }

    /**
     * @inheritDoc
     */
    public function convertFields(array $columns, array $fields, array $select = []): string
    {
        return $this->_query()->convertFields($columns, $fields, $select);
    }

    /**
     * @inheritDoc
     */
    public function unconvertField(TableFieldDto $field, string $value): string
    {
        return $this->_query()->unconvertField($field, $value);
    }
}
