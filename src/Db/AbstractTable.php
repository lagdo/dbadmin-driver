<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\Driver\TableInterface;
use Lagdo\DbAdmin\Driver\Entity\PartitionEntity;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\Entity\TriggerEntity;
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
    public function tableStatusOrName(string $table, bool $fast = false): TableEntity
    {
        if (($status = $this->tableStatus($table, $fast))) {
            return $status;
        }
        return new TableEntity($table);
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
    public function supportForeignKeys(TableEntity $tableStatus): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function partitionsInfo(string $table): PartitionEntity|null
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function isView(TableEntity $tableStatus): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function trigger(string $name, string $table = ''): TriggerEntity|null
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
