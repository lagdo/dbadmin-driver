<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Statement;

use Lagdo\DbAdmin\Driver\Sql\Dto\TableDto;

abstract class AbstractDatabase extends AbstractStatement implements DatabaseInterface
{
    /**
     * @inheritDoc
     */
    public function getForeignKeyQueries(TableDto $table): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getCreateIndexQuery(string $table, string $type, string $name, string $columns): string
    {
        return '';
    }
}
