<?php

namespace Lagdo\DbAdmin\Support\Db\Engine\Driver;

use Lagdo\DbAdmin\Support\Db\Admin\Driver\TableInterface;
use Lagdo\DbAdmin\Support\DriverInterface;
use Lagdo\DbAdmin\Support\Dto\PartitionDto;
use Lagdo\DbAdmin\Support\Dto\TableDto;
use Lagdo\DbAdmin\Support\Dto\TriggerDto;
use Lagdo\DbAdmin\Support\GrammarInterface;
use Lagdo\DbAdmin\Support\Utils\Utils;

abstract class AbstractTable implements TableInterface
{
    /**
     * @param DriverInterface $driver
     * @param GrammarInterface $grammar
     * @param Utils $utils
     */
    public function __construct(protected DriverInterface $driver,
        protected GrammarInterface $grammar, protected Utils $utils)
    {}

    /**
     * @inheritDoc
     */
    public function tableStatusOrName(string $table, bool $fast = false): TableDto
    {
        if (($status = $this->tableStatus($table, $fast))) {
            return $status;
        }
        return new TableDto($table);
    }

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
