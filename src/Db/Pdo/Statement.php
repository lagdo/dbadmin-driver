<?php

namespace Lagdo\DbAdmin\Driver\Db\Pdo;

use Lagdo\DbAdmin\Driver\Db\StatementInterface;

use PDOStatement;
use PDO;

class Statement extends PDOStatement implements StatementInterface
{
    /**
     * Undocumented variable
     *
     * @var int
     */
    public $offset = 0;

    /**
     * Undocumented variable
     *
     * @var int
     */
    public $numRows;

    public function fetchAssoc()
    {
        return $this->fetch(PDO::FETCH_ASSOC);
    }

    public function fetchRow()
    {
        return $this->fetch(PDO::FETCH_NUM);
    }

    public function fetchField()
    {
        $row = (object) $this->getColumnMeta($this->offset++);
        $row->orgtable = $row->table;
        $row->orgname = $row->name;
        $row->charsetnr = (in_array("blob", (array) $row->flags) ? 63 : 0);
        return $row;
    }
}
