<?php

namespace Lagdo\DbAdmin\Driver\Db\Pdo;

use PDOStatement;
use PDO;

class Statement extends PDOStatement
{
    /**
     * Undocumented variable
     *
     * @var int
     */
    public $_offset = 0;

    /**
     * Undocumented variable
     *
     * @var int
     */
    public $num_rows;

    public function fetch_assoc()
    {
        return $this->fetch(PDO::FETCH_ASSOC);
    }

    public function fetch_row()
    {
        return $this->fetch(PDO::FETCH_NUM);
    }

    public function fetch_field()
    {
        $row = (object) $this->getColumnMeta($this->_offset++);
        $row->orgtable = $row->table;
        $row->orgname = $row->name;
        $row->charsetnr = (in_array("blob", (array) $row->flags) ? 63 : 0);
        return $row;
    }
}
