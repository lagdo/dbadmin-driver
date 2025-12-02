<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Lagdo\DbAdmin\Driver\Db\StatementInterface;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Exception;

trait QueryTrait
{
    /**
     * @var QueryInterface
     */
    abstract protected function _query(): QueryInterface;

    /**
     * Select data from table
     *
     * @param string $table
     * @param array $select Result of processSelectColumns()[0]
     * @param array $where Result of processSelectWhere()
     * @param array $group Result of processSelectColumns()[1]
     * @param array $order Result of processSelectOrder()
     * @param int $limit Result of processSelectLimit()
     * @param int $page Index of page starting at zero
     *
     * @return StatementInterface|bool
     */
    public function select(string $table, array $select, array $where,
        array $group, array $order = [], int $limit = 1, int $page = 0)
    {
        return $this->_query()->select($table, $select, $where, $group, $order, $limit, $page);
    }

    /**
     * Insert data into table
     *
     * @param string $table
     * @param array $values Escaped columns in keys, quoted data in values
     *
     * @return bool
     */
    public function insert(string $table, array $values)
    {
        return $this->_query()->insert($table, $values);
    }

    /**
     * Update data in table
     *
     * @param string $table
     * @param array $values Escaped columns in keys, quoted data in values
     * @param string $queryWhere " WHERE ..."
     * @param int $limit 0 or 1
     *
     * @return bool
     */
    public function update(string $table, array $values, string $queryWhere, int $limit = 0)
    {
        return $this->_query()->update($table, $values, $queryWhere, $limit);
    }

    /**
     * Delete data from table
     *
     * @param string $table
     * @param string $queryWhere " WHERE ..."
     * @param int $limit 0 or 1
     *
     * @return bool
     */
    public function delete(string $table, string $queryWhere, int $limit = 0)
    {
        return $this->_query()->delete($table, $queryWhere, $limit);
    }

    /**
     * Insert or update data in table
     *
     * @param string $table
     * @param array $rows
     * @param array $primary of arrays with escaped columns in keys and quoted data in values
     *
     * @return bool
     */
    public function insertOrUpdate(string $table, array $rows, array $primary)
    {
        return $this->_query()->insertOrUpdate($table, $rows, $primary);
    }

    /**
     * Get last auto increment ID
     *
     * @return string
     */
    public function lastAutoIncrementId()
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
    public function slowQuery(string $query, int $timeout)
    {
        return $this->_query()->slowQuery($query, $timeout);
    }

    /**
     * Execute query
     *
     * @param string $query
     * @param bool $execute
     * @param bool $failed
     *
     * @return bool
     * @throws Exception
     */
    public function executeQuery(string $query, bool $execute = true,
        bool $failed = false/*, string $time = ''*/): bool
    {
        return $this->_query()->executeQuery($query, $execute, $failed/*, $time*/);
    }

    /**
     * Create SQL condition from parsed query string
     *
     * @param array $where Parsed query string
     * @param array $fields
     *
     * @return string
     */
    public function where(array $where, array $fields = []): string
    {
        return $this->_query()->where($where, $fields);
    }

    /**
     * Get approximate number of rows
     *
     * @param TableEntity $tableStatus
     * @param array $where
     *
     * @return int|null
     */
    public function countRows(TableEntity $tableStatus, array $where)
    {
        return $this->_query()->countRows($tableStatus, $where);
    }

    /**
     * @inheritDoc
     */
    public function applyQueries(string $query, array $tables, $escape = null)
    {
        return $this->_query()->applyQueries($query, $tables, $escape);
    }

    /**
     * @inheritDoc
     */
    public function values(string $query, int $column = 0)
    {
        return $this->_query()->values($query, $column);
    }

    /**
     * @inheritDoc
     */
    public function colValues(string $query, string $column)
    {
        return $this->_query()->colValues($query, $column);
    }

    /**
     * @inheritDoc
     */
    public function rows(string $query): array
    {
        return $this->_query()->rows($query);
    }

    /**
     * @inheritDoc
     */
    public function keyValues(string $query, bool $setKeys = true)
    {
        return $this->_query()->keyValues($query, $setKeys);
    }

    /**
     * Convert column to be searchable
     *
     * @param string $idf Escaped column name
     * @param array $value ["op" => , "val" => ]
     * @param TableFieldEntity $field
     *
     * @return string
     */
    public function convertSearch(string $idf, array $value, TableFieldEntity $field)
    {
        return $this->_query()->convertSearch($idf, $value, $field);
    }

    /**
     * Get view SELECT
     *
     * @param string $name
     *
     * @return array array("select" => )
     */
    public function view(string $name)
    {
        return $this->_query()->view($name);
    }

    /**
     * @inheritDoc
     */
    public function execute(string $query)
    {
        return $this->_query()->execute($query);
    }

    /**
     * @inheritDoc
     */
    public function begin()
    {
        return $this->_query()->begin();
    }

    /**
     * @inheritDoc
     */
    public function commit()
    {
        return $this->_query()->commit();
    }

    /**
     * @inheritDoc
     */
    public function rollback()
    {
        return $this->_query()->rollback();
    }
}
