<?php

namespace Lagdo\DbAdmin\Driver\Sql\Connection\Pdo;

use Lagdo\DbAdmin\Driver\Sql\Connection\QueryResultInterface;
use Lagdo\DbAdmin\Driver\Sql\Dto\ResultColumnDto;
use PDOStatement;
use PDO;

use function in_array;
use function is_bool;

class QueryResult implements QueryResultInterface
{
    /**
     * @var PDOStatement|null
     */
    private PDOStatement|null $pdo = null;

    /**
     * @var bool
     */
    private bool $hasError;

    /**
     * @var int
     */
    private int $columnOffset = 0;

    /**
     * @param PDOStatement|bool $result
     */
    public function __construct(PDOStatement|bool $result)
    {
        $isBool = is_bool($result);
        $this->hasError = $isBool ? !$result : false;
        if (!$isBool) {
            $this->pdo = $result;
        }
    }

    /**
     * @return PDOStatement|null
     */
    public function statement(): PDOStatement|null
    {
        return $this->pdo;
    }

    /**
     * @inheritDoc
     */
    public function hasError(): bool
    {
        return $this->hasError;
    }

    /**
     * @inheritDoc
     */
    public function hasRowset(): bool
    {
        return $this->pdo !== null;
    }

    /**
     * @inheritDoc
     */
    public function rowCount(): int
    {
        return $this->pdo?->rowCount() ?? 0;
    }

    /**
     * @inheritDoc
     */
    public function fetchAssoc(): array|null
    {
        return $this->pdo?->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * @inheritDoc
     */
    public function fetchRow(): array|null
    {
        return $this->pdo?->fetch(PDO::FETCH_NUM) ?: null;
    }

    /**
     * @inheritDoc
     */
    public function fetchColumn(): ResultColumnDto|null
    {
        $row = $this->pdo?->getColumnMeta($this->columnOffset++) ?? false;
        if (!$row) {
            return null;
        }

        $isBinary = in_array("blob", (array)($row['flags'] ?? []));
        ['native_type' => $type, 'name' => $name, 'table' => $table] = $row;
        return new ResultColumnDto($type, $isBinary, $name, $name, $table, $table);
    }
}
