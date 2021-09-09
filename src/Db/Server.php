<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\Entity\IndexEntity;
use Lagdo\DbAdmin\Driver\Entity\ForeignKeyEntity;
use Lagdo\DbAdmin\Driver\Entity\TriggerEntity;
use Lagdo\DbAdmin\Driver\Entity\RoutineEntity;

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
    public function minVersion(string $version, string $mariaDb = "", ConnectionInterface $connection = null)
    {
        if (!$connection) {
            $connection = $this->connection;
        }
        $info = $connection->serverInfo();
        if ($mariaDb && preg_match('~([\d.]+)-MariaDB~', $info, $match)) {
            $info = $match[1];
            $version = $mariaDb;
        }
        return (version_compare($info, $version) >= 0);
    }

    /**
     * @inheritDoc
     */
    public function charset()
    {
        // SHOW CHARSET would require an extra query
        return ($this->minVersion("5.5.3", 0) ? "utf8mb4" : "utf8");
    }

    /**
     * @inheritDoc
     */
    public function setUtf8mb4(string $create)
    {
        static $set = false;
        // possible false positive
        if (!$set && preg_match('~\butf8mb4~i', $create)) {
            $set = true;
            return "SET NAMES " . $this->charset() . ";\n\n";
        }
        return '';
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
