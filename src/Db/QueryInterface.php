<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;

use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;

interface QueryInterface
{
    /**
     * Get logged user
     *
     * @return string
     */
    public function user();

    /**
     * Get current schema from the database
     *
     * @return string
     */
    public function schema();

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
        array $group, array $order = [], int $limit = 1, int $page = 0);

    /**
     * Insert data into table
     *
     * @param string $table
     * @param array $set Escaped columns in keys, quoted data in values
     *
     * @return bool
     */
    public function insert(string $table, array $set);

    /**
     * Update data in table
     *
     * @param string $table
     * @param array $set Escaped columns in keys, quoted data in values
     * @param string $queryWhere " WHERE ..."
     * @param int $limit 0 or 1
     * @param string $separator
     *
     * @return bool
     */
    public function update(string $table, array $set, string $queryWhere, int $limit = 0, string $separator = "\n");

    /**
     * Delete data from table
     *
     * @param string $table
     * @param string $queryWhere " WHERE ..."
     * @param int $limit 0 or 1
     *
     * @return bool
     */
    public function delete(string $table, string $queryWhere, int $limit = 0);

    /**
     * Insert or update data in table
     *
     * @param string $table
     * @param array $rows
     * @param array $primary of arrays with escaped columns in keys and quoted data in values
     *
     * @return bool
     */
    public function insertOrUpdate(string $table, array $rows, array $primary);

    /**
     * Get last auto increment ID
     *
     * @return string
     */
    public function lastAutoIncrementId();

    /**
     * Return query with a timeout
     *
     * @param string $query
     * @param int $timeout In seconds
     *
     * @return string|null
     */
    public function slowQuery(string $query, int $timeout);

    /**
     * Begin transaction
     *
     * @return bool
     */
    public function begin();

    /**
     * Commit transaction
     *
     * @return bool
     */
    public function commit();

    /**
     * Rollback transaction
     *
     * @return bool
     */
    public function rollback();

    /**
     * Set the error message
     *
     * @param string $error
     *
     * @return void
     */
    public function setError(string $error = '');

    /**
     * Get the raw error message
     *
     * @return string
     */
    public function error();

    /**
     * Check if the last query returned an error message
     *
     * @return bool
     */
    public function hasError();

    /**
     * Set the error number
     *
     * @param int $errno
     *
     * @return void
     */
    public function setErrno(int $errno);

    /**
     * Get the last error number
     *
     * @return string
     */
    public function errno();

    /**
     * Check if the last query returned an error number
     *
     * @return bool
     */
    public function hasErrno();

    /**
     * Set the number of rows affected by the last query
     *
     * @param int $affectedRows
     *
     * @return void
     */
    public function setAffectedRows(int $affectedRows);

    /**
     * Get the number of rows affected by the last query
     *
     * @return integer
     */
    public function affectedRows();

    /**
     * Execute and remember query
     *
     * @param string $query
     *
     * @return StatementInterface|bool
     */
    public function execute(string $query);

    /**
     * Get the remembered queries
     *
     * @return array
     */
    public function queries();

    /**
     * Apply command to all array items
     *
     * @param string $query
     * @param array $tables
     * @param callback|null $escape
     *
     * @return bool
     */
    public function applyQueries(string $query, array $tables, $escape = null);

    /**
     * Get list of values from database
     *
     * @param string $query
     * @param string|int $column
     *
     * @return array
     */
    public function values(string $query, $column = 0);

    /**
     * Get keys from first column and values from second
     *
     * @param string $query
     * @param ConnectionInterface $connection
     * @param bool $setKeys
     *
     * @return array
     */
    public function keyValues(string $query, ConnectionInterface $connection = null, bool $setKeys = true);

    /**
     * Get all rows of result
     *
     * @param string $query
     * @param ConnectionInterface $connection
     *
     * @return array
     */
    public function rows(string $query, ConnectionInterface $connection = null);

    /**
     * Remove current user definer from SQL command
     *
     * @param string $query
     *
     * @return string
     */
    public function removeDefiner(string $query);

    /**
     * Explain select
     *
     * @param ConnectionInterface $connection
     * @param string $query
     *
     * @return StatementInterface|bool
     */
    public function explain(ConnectionInterface $connection, string $query);

    /**
     * Get approximate number of rows
     *
     * @param TableEntity $tableStatus
     * @param array $where
     *
     * @return int|null
     */
    public function countRows(TableEntity $tableStatus, array $where);

    /**
     * Convert column to be searchable
     *
     * @param string $idf escaped column name
     * @param array $val array("op" => , "val" => )
     * @param TableFieldEntity $field
     *
     * @return string
     */
    public function convertSearch(string $idf, array $val, TableFieldEntity $field);

    /**
     * Get view SELECT
     *
     * @param string $name
     *
     * @return array array("select" => )
     */
    public function view(string $name);
}
