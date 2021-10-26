<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Entity\ConfigEntity;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Entity\ForeignKeyEntity;
use Lagdo\DbAdmin\Driver\Entity\TriggerEntity;
use Lagdo\DbAdmin\Driver\Entity\RoutineEntity;

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
     * @param array $where Result of processSelectSearch()
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
     * @param array $set Escaped columns in keys, quoted data in values
     *
     * @return StatementInterface|bool
     */
    public function insert(string $table, array $set)
    {
        return $this->query->insert($table, $set);
    }

    /**
     * Update data in table
     *
     * @param string $table
     * @param array $set Escaped columns in keys, quoted data in values
     * @param string $queryWhere " WHERE ..."
     * @param int $limit 0 or 1
     * @param string $separator
     *
     * @return StatementInterface|bool
     */
    public function update(string $table, array $set, string $queryWhere, int $limit = 0, string $separator = "\n")
    {
        return $this->query->update($table, $set, $queryWhere, $limit, $separator);
    }

    /**
     * Delete data from table
     *
     * @param string $table
     * @param string $queryWhere " WHERE ..."
     * @param int $limit 0 or 1
     *
     * @return StatementInterface|bool
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
     * @return StatementInterface|bool
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
     * Begin transaction
     *
     * @return StatementInterface|bool
     */
    public function begin()
    {
        return $this->query->begin();
    }

    /**
     * Commit transaction
     *
     * @return StatementInterface|bool
     */
    public function commit()
    {
        return $this->query->commit();
    }

    /**
     * Rollback transaction
     *
     * @return StatementInterface|bool
     */
    public function rollback()
    {
        return $this->query->rollback();
    }

    /**
     * Set the error message
     *
     * @param string $error
     *
     * @return void
     */
    public function setError(string $error = '')
    {
        return $this->query->setError($error);
    }

    /**
     * Get the raw error message
     *
     * @return string
     */
    public function error()
    {
        return $this->query->error();
    }

    /**
     * Check if the last query returned an error message
     *
     * @return bool
     */
    public function hasError()
    {
        return $this->query->hasError();
    }

    /**
     * Set the error number
     *
     * @param int $errno
     *
     * @return void
     */
    public function setErrno(int $errno)
    {
        return $this->query->setErrno($errno);
    }

    /**
     * Get the last error number
     *
     * @return string
     */
    public function errno()
    {
        return $this->query->errno();
    }

    /**
     * Check if the last query returned an error number
     *
     * @return bool
     */
    public function hasErrno()
    {
        return $this->query->hasErrno();
    }

    /**
     * Set the number of rows affected by the last query
     *
     * @param int $affectedRows
     *
     * @return void
     */
    public function setAffectedRows(int $affectedRows)
    {
        return $this->query->setAffectedRows($affectedRows);
    }

    /**
     * Get the number of rows affected by the last query
     *
     * @return integer
     */
    public function affectedRows()
    {
        return $this->query->affectedRows();
    }

    /**
     * Execute and remember query
     *
     * @param string $query
     *
     * @return StatementInterface|bool
     */
    public function execute(string $query)
    {
        return $this->query->execute($query);
    }

    /**
     * Get the remembered queries
     *
     * @return array
     */
    public function queries()
    {
        return $this->query->queries();
    }

    /**
     * Apply command to all array items
     *
     * @param string $query
     * @param array $tables
     * @param callback|null $escape
     *
     * @return bool
     */
    public function applyQueries(string $query, array $tables, $escape = null)
    {
        return $this->query->applyQueries($query, $tables, $escape);
    }

    /**
     * Get list of values from database
     *
     * @param string $query
     * @param string|int $column
     *
     * @return array
     */
    public function values(string $query, $column = 0)
    {
        return $this->query->values($query, $column);
    }

    /**
     * Get keys from first column and values from second
     *
     * @param string $query
     * @param ConnectionInterface $connection
     * @param bool $setKeys
     *
     * @return array
     */
    public function keyValues(string $query, ConnectionInterface $connection = null, bool $setKeys = true)
    {
        return $this->query->keyValues($query, $connection, $setKeys);
    }

    /**
     * Get all rows of result
     *
     * @param string $query
     * @param ConnectionInterface $connection
     *
     * @return array
     */
    public function rows(string $query, ConnectionInterface $connection = null)
    {
        return $this->query->rows($query, $connection);
    }

    /**
     * Remove current user definer from SQL command
     *
     * @param string $query
     *
     * @return string
     */
    public function removeDefiner(string $query)
    {
        return $this->query->removeDefiner($query);
    }

    /**
     * Explain select
     *
     * @param ConnectionInterface $connection
     * @param string $query
     *
     * @return Statement|null
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
