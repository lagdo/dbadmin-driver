<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\UtilInterface;
use Lagdo\DbAdmin\Driver\TranslatorInterface;

abstract class Database implements DatabaseInterface
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
    public function dropViews(array $views)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function moveTables(array $tables, array $views, string $target)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function copyTables(array $tables, array $views, string $target)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function truncateTables(array $tables)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function sequences()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function userTypes()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function schemas()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function events()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function routine(string $name, string $type)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function routines()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function routineId(string $name, array $row)
    {
        return '';
    }
}
