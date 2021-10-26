<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Entity\StatementFieldEntity;

interface StatementInterface
{
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
