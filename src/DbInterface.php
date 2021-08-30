<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;

interface DbInterface
{
    /**
     * Get the database server options
     *
     * @return array
     */
    public function getOptions();

    /**
     * Get SSL connection options
     *
     * @return array
     */
    public function connectSsl();

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
     * @param string $errno
     *
     * @return void
     */
    public function setErrno($errno);

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
     * @param integer $affected_rows
     *
     * @return void
     */
    public function setAffectedRows($affected_rows);

    /**
     * Get the number of rows affected by the last query
     *
     * @return integer
     */
    public function affectedRows();

    /**
     * Select data from table
     *
     * @param array $select
     * @param array $where
     * @param array $group
     * @param array $order
     * @param int $limit
     * @param int $page
     *
     * @return string
     */
    public function buildSelectQuery(array $select, array $where, array $group, array $order = [], $limit = 1, $page = 0);

    /**
     * Execute and remember query
     * @param string or null to return remembered queries, end with ';' to use DELIMITER
     * @return Statement or array($queries, $time) if $query = null
     */
    public function queries($query);

    /**
     * Apply command to all array items
     * @param string
     * @param array
     * @param callback|null
     * @return bool
     */
    public function apply_queries($query, $tables, $escape = null);

    /**
     * Get list of values from database
     * @param string
     * @param mixed
     * @return array
     */
    public function get_vals($query, $column = 0);

    /**
     * Get keys from first column and values from second
     * @param string
     * @param ConnectionInterface
     * @param bool
     * @return array
     */
    public function get_key_vals($query, $connection = null, $set_keys = true);

    /**
     * Get all rows of result
     * @param string
     * @param ConnectionInterface
     * @param string
     * @return array of associative arrays
     */
    public function get_rows($query, $connection = null);

    /**
     * Get default value clause
     * @param array
     * @return string
     */
    public function default_value($field);

    /**
     * Get regular expression to match numeric types
     * @return string
     */
    public function number_type();
}
