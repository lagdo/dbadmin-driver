<?php

namespace Lagdo\DbAdmin\Driver\Db;

interface DriverInterface
{
    /**
     * Return a quoted string
     *
     * @param string $string
     *
     * @return string
     */
    public function quoteBinary($string);

    /**
     * Select data from table
     * @param string $table
     * @param array $select result of $this->util->processSelectColumns()[0]
     * @param array $where result of $this->util->processSelectSearch()
     * @param array $group result of $this->util->processSelectColumns()[1]
     * @param array $order result of $this->util->processSelectOrder()
     * @param int $limit result of $this->util->processSelectLimit()
     * @param int $page index of page starting at zero
     * @return Statement
     */
    public function select($table, $select, $where, $group, $order = [], $limit = 1, $page = 0);

    /**
     * Insert or update data in table
     * @param string $table
     * @param array $rows
     * @param array $primary of arrays with escaped columns in keys and quoted data in values
     * @return bool
     */
    public function insertOrUpdate($table, $rows, $primary);

    /**
     * Get warnings about the last command
     * @return string
     */
    public function warnings();
}
