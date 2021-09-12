<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\UtilInterface;
use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;

abstract class Server implements ServerInterface
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
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * The constructor
     *
     * @param DriverInterface $driver
     * @param UtilInterface $util
     * @param ConnectionInterface $connection
     */
    public function __construct(DriverInterface $driver, UtilInterface $util, ConnectionInterface $connection)
    {
        $this->driver = $driver;
        $this->util = $util;
        $this->connection = $connection;
    }

    /**
     * @inheritDoc
     */
    public function engines()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function collations()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function databaseCollation(string $database, array $collations)
    {
        return "";
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
    public function isInformationSchema(string $database)
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
    public function dropViews(array $views)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function variables()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function statusVariables()
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
    public function routineLanguages()
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

    /**
     * @inheritDoc
     */
    public function renameDatabase(string $name, string $collation)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function processes()
    {
        return [];
    }
}
