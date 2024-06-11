<?php

namespace Lagdo\DbAdmin\Driver;

use Exception;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;
use Lagdo\DbAdmin\Driver\Db\StatementInterface;

trait QueryTrait
{
    /**
     * Get logged user
     *
     * @return string
     */
    public function user()
    {
        return $this->query->user();
    }

    /**
     * Get current schema from the database
     *
     * @return string
     */
    // public function schema()
    // {
    //     return $this->query->schema();
    // }

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
        return $this->query->select($table, $select, $where, $group, $order, $limit, $page);
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
        return $this->query->insert($table, $values);
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
        return $this->query->update($table, $values, $queryWhere, $limit);
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
        return $this->query->delete($table, $queryWhere, $limit);
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
        return $this->query->insertOrUpdate($table, $rows, $primary);
    }

    /**
     * Get last auto increment ID
     *
     * @return string
     */
    public function lastAutoIncrementId()
    {
        return $this->query->lastAutoIncrementId();
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
        return $this->query->slowQuery($query, $timeout);
    }

    /**
     * Remove current user definer from SQL command
     *
     * @param string $query
     *
     * @return string
     */
    public function removeDefiner(string $query): string
    {
        return $this->query->removeDefiner($query);
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
        return $this->query->executeQuery($query, $execute, $failed/*, $time*/);
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
        return $this->query->where($where, $fields);
    }

    /**
     * Explain select
     *
     * @param ConnectionInterface $connection
     * @param string $query
     *
     * @return StatementInterface|bool
     */
    public function explain(ConnectionInterface $connection, string $query)
    {
        return $this->query->explain($connection, $query);
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
        return $this->query->countRows($tableStatus, $where);
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
        return $this->query->convertSearch($idf, $value, $field);
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
        return $this->query->view($name);
    }
}
