<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Entity\StatementFieldEntity;

interface StatementInterface
{
    /**
     * Get the number of rows returned by the query
     *
     * @return int
     */
    public function rowCount();

    /**
     * Fetch the next row as an array with field position as keys
     *
     * @return array
     */
    public function fetchRow();

    /**
     * Fetch the next row as an array with field name as keys
     *
     * @return array
     */
    public function fetchAssoc();

    /**
     * Fetch the next field
     *
     * @return StatementFieldEntity
     */
    public function fetchField();
}
