<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\Driver\TableInterface;
use Lagdo\DbAdmin\Driver\Dto\PartitionDto;
use Lagdo\DbAdmin\Driver\Dto\TableDto;
use Lagdo\DbAdmin\Driver\Dto\TriggerDto;
use Lagdo\DbAdmin\Driver\Utils\Utils;

abstract class AbstractTable implements TableInterface
{
    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var Utils
     */
    protected $utils;

    /**
     * The constructor
     *
     * @param DriverInterface $driver
     * @param Utils $utils
     */
    public function __construct(DriverInterface $driver, Utils $utils)
    {
        $this->driver = $driver;
        $this->utils = $utils;
    }

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
