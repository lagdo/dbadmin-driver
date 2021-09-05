<?php

namespace Lagdo\DbAdmin\Driver\Db;

interface StatementInterface
{
    /**
     * Fetch the next row as an array with position as keys
     *
     * @return object
     */
    public function fetchRow();

    /**
     * Fetch the next row as an array with name as keys
     *
     * @return array
     */
    public function fetchAssoc();

    /**
     * Fetch the next field
     *
     * @return StatementField
     */
    public function fetchField();
}
