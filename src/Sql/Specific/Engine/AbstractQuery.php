<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Engine;

use Lagdo\DbAdmin\Driver\Sql\AbstractDbProxy;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableDto;

abstract class AbstractQuery extends AbstractDbProxy implements QueryInterface
{
    /**
     * @inheritDoc
     */
    public function slowQuery(string $query, int $timeout): string|null
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function countRows(TableDto $tableStatus, array $where): int|null
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function convertSearch(string $idf, array $value, ColumnDto $column): string
    {
        return $idf;
    }

    /**
     * @inheritDoc
     */
    public function view(string $name): array
    {
        return [];
    }
}
