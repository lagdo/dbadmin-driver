<?php

namespace Lagdo\DbAdmin\Support\Db\Engine\Driver;

use Lagdo\DbAdmin\Support\AbstractDriver;
use Lagdo\DbAdmin\Support\AbstractGrammar;
use Lagdo\DbAdmin\Support\Dto\PartitionDto;
use Lagdo\DbAdmin\Support\Dto\TableDto;
use Lagdo\DbAdmin\Support\Dto\TriggerDto;
use Lagdo\DbAdmin\Support\Utils\Utils;

abstract class AbstractTable implements TableInterface
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
    public function foreignKeys(string $table): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function supportForeignKeys(TableDto $tableStatus): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function partitionsInfo(string $table): PartitionDto|null
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function isView(TableDto $tableStatus): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function trigger(string $name, string $table = ''): TriggerDto|null
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function triggers(string $table): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function triggerOptions(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function tableHelp(string $name): string
    {
        return '';
    }
}
