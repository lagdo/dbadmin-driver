<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\UtilInterface;
use Lagdo\DbAdmin\Driver\TranslatorInterface;
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
     * @var TranslatorInterface
     */
    protected $trans;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * The constructor
     *
     * @param DriverInterface $driver
     * @param UtilInterface $util
     * @param TranslatorInterface $trans
     * @param ConnectionInterface $connection
     */
    public function __construct(DriverInterface $driver, UtilInterface $util, TranslatorInterface $trans, ConnectionInterface $connection)
    {
        $this->driver = $driver;
        $this->util = $util;
        $this->trans = $trans;
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
    public function events()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function privileges()
    {
        // From user.inc.php
        $features = [
            "" => [
                "All privileges" => "",
            ],
        ];
        foreach ($this->driver->rows("SHOW PRIVILEGES") as $row) {
            // Context of "Grant option" privilege is set to empty string
            $contexts = \explode(",", ($row["Privilege"] == "Grant option" ? "" : $row["Context"]));
            foreach ($contexts as $context) {
                $features[$context][$row["Privilege"]] = $row["Comment"];
            }
        }

        // Privileges of "Server Admin" and "File access on server" are merged
        $features["Server Admin"] = \array_merge(
            $features["Server Admin"],
            $features["File access on server"]
        );
        // Comment for this is "No privileges - allow connect only"
        unset($features["Server Admin"]["Usage"]);

        if (\array_key_exists("Create routine", $features["Procedures"])) {
            // MySQL bug #30305
            $features["Databases"]["Create routine"] = $features["Procedures"]["Create routine"];
            unset($features["Procedures"]["Create routine"]);
        }

        $features["Columns"] = [];
        foreach (["Select", "Insert", "Update", "References"] as $val) {
            $features["Columns"][$val] = $features["Tables"][$val];
        }

        foreach ($features["Tables"] as $key => $val) {
            unset($features["Databases"][$key]);
        }

        return (array)$features;
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
