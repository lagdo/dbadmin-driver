<?php

namespace Lagdo\DbAdmin\Driver\Db\Pdo;

use Lagdo\DbAdmin\Driver\Db\StatementInterface;
use Lagdo\DbAdmin\Driver\Db\StatementField;

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
    public $numRows = 0;

    /**
     * @inheritDoc
     */
    public function fetchAssoc()
    {
        return $this->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @inheritDoc
     */
    public function fetchRow()
    {
        return $this->fetch(PDO::FETCH_NUM);
    }

    /**
     * @inheritDoc
     */
    public function fetchField()
    {
        $row = $this->getColumnMeta($this->offset++);
        return new StatementField($row['native_type'], in_array("blob", (array)$row['flags']),
            $row['name'], $row['name'], $row['table'], $row['table']);
    }
}
