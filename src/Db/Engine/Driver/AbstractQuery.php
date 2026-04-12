<?php

namespace Lagdo\DbAdmin\Support\Db\Engine\Driver;

use Lagdo\DbAdmin\Support\Db\AbstractDbProxy;
use Lagdo\DbAdmin\Support\Dto\TableDto;
use Lagdo\DbAdmin\Support\Dto\TableFieldDto;

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
    public function convertSearch(string $idf, array $value, TableFieldDto $field): string
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
