<?php

namespace Lagdo\DbAdmin\Support\Db\Engine\Driver;

use Lagdo\DbAdmin\Support\AbstractDriver;
use Lagdo\DbAdmin\Support\AbstractGrammar;
use Lagdo\DbAdmin\Support\Dto\TableDto;
use Lagdo\DbAdmin\Support\Dto\TableFieldDto;
use Lagdo\DbAdmin\Support\Utils\Utils;

abstract class AbstractQuery implements QueryInterface
{
    /**
     * @param AbstractDriver $driver
     * @param AbstractGrammar $grammar
     * @param Utils $utils
     */
    public function __construct(protected AbstractDriver $driver,
        protected AbstractGrammar $grammar, protected Utils $utils)
    {}

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
