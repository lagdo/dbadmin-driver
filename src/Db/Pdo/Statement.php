<?php

namespace Lagdo\DbAdmin\Driver\Db\Pdo;

use Lagdo\DbAdmin\Driver\Db\StatementInterface;
use Lagdo\DbAdmin\Driver\Entity\StatementFieldEntity;

use PDOStatement;
use PDO;

class Statement extends PDOStatement implements StatementInterface
{
    /**
     * @var int
     */
    public $offset = 0;

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
        $flags = $row['flags'] ?? [];
        return new StatementFieldEntity($row['native_type'], in_array("blob", (array)$flags),
            $row['name'], $row['name'], $row['table'], $row['table']);
    }
}
