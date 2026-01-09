<?php

namespace Lagdo\DbAdmin\Driver\Db\Pdo;

use Lagdo\DbAdmin\Driver\Db\StatementInterface;
use Lagdo\DbAdmin\Driver\Dto\StatementFieldDto;
use PDOStatement;
use PDO;

class Statement extends PDOStatement implements StatementInterface
{
    /**
     * @var int
     */
    public $offset = 0;

    /**
     * @var int
     */
    public $numRows = 0;

    /**
     * @inheritDoc
     */
    public function fetchAssoc(): array|null
    {
        return $this->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * @inheritDoc
     */
    public function fetchRow(): array|null
    {
        return $this->fetch(PDO::FETCH_NUM) ?: null;
    }

    /**
     * @inheritDoc
     */
    public function fetchField(): StatementFieldDto
    {
        $row = $this->getColumnMeta($this->offset++);
        $flags = $row['flags'] ?? [];
        return new StatementFieldDto($row['native_type'], in_array("blob", (array)$flags),
            $row['name'], $row['name'], $row['table'], $row['table']);
    }
}
