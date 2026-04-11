<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Driver;

use Lagdo\DbAdmin\Support\Db\Engine\Connection\StatementInterface;
use Exception;

trait QueryTrait
{
    /**
     * @var QueryInterface
     */
    private QueryInterface $query;

    /**
     * @return QueryInterface
     */
    private function _q(): QueryInterface
    {
        return $this->query ??= new Query($this, $this->grammar(), $this->utils);
    }

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
    public function select(string $table, array $select, array $where, array $group = [],
        array $order = [], int $limit = 1, int $page = 0): StatementInterface|bool
    {
        return $this->_q()->select($table, $select, $where, $group, $order, $limit, $page);
    }

    /**
     * Insert data into table
     *
     * @param string $table
     * @param array $values Escaped columns in keys, quoted data in values
     *
     * @return bool
     */
    public function insert(string $table, array $values): bool
    {
        return $this->_q()->insert($table, $values);
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
    public function update(string $table, array $values, string $queryWhere, int $limit = 0): bool
    {
        return $this->_q()->update($table, $values, $queryWhere, $limit);
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
    public function delete(string $table, string $queryWhere, int $limit = 0): bool
    {
        return $this->_q()->delete($table, $queryWhere, $limit);
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
    // public function insertOrUpdate(string $table, array $rows, array $primary): bool
    // {
    //     return $this->_q()->insertOrUpdate($table, $rows, $primary);
    // }

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
        return $this->_q()->executeQuery($query, $execute, $failed/*, $time*/);
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
        return $this->_q()->where($where, $fields);
    }

    /**
     * @inheritDoc
     */
    public function applyQueries(string $query, array $tables, $escape = null): bool
    {
        return $this->_q()->applyQueries($query, $tables, $escape);
    }

    /**
     * @inheritDoc
     */
    public function values(string $query, int $column = 0): array
    {
        return $this->_q()->values($query, $column);
    }

    /**
     * @inheritDoc
     */
    public function colValues(string $query, string $column): array
    {
        return $this->_q()->colValues($query, $column);
    }

    /**
     * @inheritDoc
     */
    public function rows(string $query): array
    {
        return $this->_q()->rows($query);
    }

    /**
     * @inheritDoc
     */
    public function keyValues(string $query, bool $setKeys = true): array
    {
        return $this->_q()->keyValues($query, $setKeys);
    }

    /**
     * @inheritDoc
     */
    public function execute(string $query): StatementInterface|bool
    {
        return $this->_q()->execute($query);
    }

    /**
     * @inheritDoc
     */
    public function begin(): bool
    {
        return $this->_q()->begin();
    }

    /**
     * @inheritDoc
     */
    public function commit(): bool
    {
        return $this->_q()->commit();
    }

    /**
     * @inheritDoc
     */
    public function rollback(): bool
    {
        return $this->_q()->rollback();
    }
}
