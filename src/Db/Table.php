<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\Utils\Utils;

abstract class Table implements TableInterface
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
    public function tableStatusOrName(string $table, bool $fast = false)
    {
        if (($status = $this->tableStatus($table, $fast))) {
            return $status;
        }
        return new TableEntity($table);
    }

    /**
     * @inheritDoc
     */
    public function foreignKeys(string $table)
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function supportForeignKeys(TableEntity $tableStatus)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isView(TableEntity $tableStatus)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function trigger(string $name, string $table = '')
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function triggers(string $table)
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function triggerOptions()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function referencableTables(string $table)
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function tableHelp(string $name)
    {
        return '';
    }
}
