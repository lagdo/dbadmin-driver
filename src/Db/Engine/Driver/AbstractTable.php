<?php

namespace Lagdo\DbAdmin\Support\Db\Engine\Driver;

use Lagdo\DbAdmin\Support\Db\AbstractDbProxy;
use Lagdo\DbAdmin\Support\Dto\PartitionDto;
use Lagdo\DbAdmin\Support\Dto\TableDto;
use Lagdo\DbAdmin\Support\Dto\TriggerDto;

abstract class AbstractTable extends AbstractDbProxy implements TableInterface
{
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
