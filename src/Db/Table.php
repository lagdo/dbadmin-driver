<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\UtilInterface;
use Lagdo\DbAdmin\Driver\TranslatorInterface;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;

abstract class Table implements TableInterface
{
    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var UtilInterface
     */
    protected $util;

    /**
     * @var TranslatorInterface
     */
    protected $trans;

    /**
     * The constructor
     *
     * @param DriverInterface $driver
     * @param UtilInterface $util
     * @param TranslatorInterface $trans
     */
    public function __construct(DriverInterface $driver, UtilInterface $util, TranslatorInterface $trans)
    {
        $this->driver = $driver;
        $this->util = $util;
        $this->trans = $trans;
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
