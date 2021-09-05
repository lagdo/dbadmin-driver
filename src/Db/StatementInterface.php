<?php

namespace Lagdo\DbAdmin\Driver\Db;

interface StatementInterface
{
    public function fetchRow();

    public function fetchAssoc();

    public function fetchField();
}
