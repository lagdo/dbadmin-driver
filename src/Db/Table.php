<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\AdminInterface;
use Lagdo\DbAdmin\Driver\TranslatorInterface;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;

abstract class Table implements TableInterface
{
    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var AdminInterface
     */
    protected $admin;

    /**
     * @var TranslatorInterface
     */
    protected $trans;

    /**
     * The constructor
     *
     * @param DriverInterface $driver
     * @param AdminInterface $admin
     * @param TranslatorInterface $trans
     */
    public function __construct(DriverInterface $driver, AdminInterface $admin, TranslatorInterface $trans)
    {
        $this->driver = $driver;
        $this->admin = $admin;
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
