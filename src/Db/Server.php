<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\DbInterface;
use Lagdo\DbAdmin\Driver\UtilInterface;

abstract class Server implements ServerInterface
{
    /**
     * @var DbInterface
     */
    protected $db = null;

    /**
     * @var UtilInterface
     */
    protected $util = null;

    /**
     * @var DriverInterface
     */
    protected $driver = null;

    /**
     * @var ConnectionInterface
     */
    protected $connection = null;

    /**
     * @var string
     */
    protected $database = '';

    /**
     * @var string
     */
    protected $schema = '';

    /**
     * From bootstrap.inc.php
     * @var string
     */
    public $onActions = "RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT"; ///< @var string used in foreignKeys()

    /**
     * From index.php
     * @var string
     */
    public $enumLength = "'(?:''|[^'\\\\]|\\\\.)*'";

    /**
     * From index.php
     * @var string
     */
    public $inout = "IN|OUT|INOUT";

    /**
     * The constructor
     *
     * @param DbInterface $db
     * @param UtilInterface $util
     */
    public function __construct(DbInterface $db, UtilInterface $util)
    {
        $this->db = $db;
        $this->util = $util;

        // From bootstrap.inc.php
        $config = $this->driverConfig();
        $this->possibleDrivers = $config['possibleDrivers'];
        $this->jush = $config['jush'];
        $this->types = $config['types'];
        $this->structuredTypes = $config['structuredTypes'];
        $this->unsigned = $config['unsigned'];
        $this->operators = $config['operators'];
        $this->functions = $config['functions'];
        $this->grouping = $config['grouping'];
        $this->editFunctions = $config['editFunctions'];

        $this->connect();
    }

    /**
     * @inheritDoc
     */
    public function connection()
    {
        return $this->connection;
    }

    /**
     * @inheritDoc
     */
    public function driver()
    {
        return $this->driver;
    }

    /**
     * @inheritDoc
     */
    public function selectDatabase(string $database, string $schema)
    {
        $this->database = $database;
        $this->schema = $schema;
        if ($database !== '') {
            $this->connection->selectDatabase($database);
            if ($schema !== '') {
                $this->selectSchema($schema);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function selectedDatabase()
    {
        return $this->database;
    }

    /**
     * @inheritDoc
     */
    public function selectedSchema()
    {
        return $this->schema;
    }

    /**
     * @inheritDoc
     */
    public function primaryIdName()
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function escapeId($idf)
    {
        return $idf;
    }

    /**
     * @inheritDoc
     */
    public function unescapeId($idf)
    {
        $last = substr($idf, -1);
        return str_replace($last . $last, $last, substr($idf, 1, -1));
    }

    /**
     * @inheritDoc
     */
    public function table($idf)
    {
        return $this->escapeId($idf);
    }

    /**
     * @inheritDoc
     */
    public function view($name)
    {
        return [];
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
    public function databaseCollation($db, $collations)
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
    public function schema()
    {
        return "";
    }

    /**
     * @inheritDoc
     */
    public function selectSchema($schema, $connection = null)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isInformationSchema($db)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isView($tableStatus)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function foreignKeys($table)
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function supportForeignKeys($tableStatus)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function convertField(array $field)
    {
    }

    /**
     * @inheritDoc
     */
    public function unconvertField(array $field, $return)
    {
        return $return;
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
    public function processes()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function explain($connection, $query)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function createTableSql($table, $auto_increment, $style)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function moveTables($tables, $views, $target)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function copyTables($tables, $views, $target)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function trigger($name)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function triggers($table)
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
    public function routine($name, $type)
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
    public function routineId($name, $row)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function useDatabaseSql($database)
    {
        return "";
    }

    /**
     * @inheritDoc
     */
    public function foreignKeysSql($table)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function truncateTableSql($table)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function createTriggerSql($table)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function lastAutoIncrementId()
    {
        return $this->connection->last_id;
    }

    /**
     * @inheritDoc
     */
    public function countRows($tableStatus, $where)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function renameDatabase($name, $collation)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function autoIncrement()
    {
        return "";
    }

    /**
     * @inheritDoc
     */
    public function alterIndexes($table, $alter)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function dropViews($views)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function truncateTables($tables)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function limit($query, $where, $limit, $offset = 0, $separator = " ")
    {
        return "";
    }

    /**
     * @inheritDoc
     */
    public function limitToOne($table, $query, $where, $separator = "\n")
    {
        return $this->limit($query, $where, 1, 0, $separator);
    }

    /**
     * @inheritDoc
     */
    public function tableStatusOrName($table, $fast = false)
    {
        $return = $this->tableStatus($table, $fast);
        return ($return ? $return : array("Name" => $table));
    }

    /**
     * @inheritDoc
     */
    public function formatForeignKey($foreignKey)
    {
        $db = $foreignKey["db"];
        $ns = $foreignKey["ns"];
        return " FOREIGN KEY (" . implode(", ", array_map(function ($idf) {
            return $this->escapeId($idf);
        }, $foreignKey["source"])) . ") REFERENCES " .
            ($db != "" && $db != $this->database ? $this->escapeId($db) . "." : "") .
            ($ns != "" && $ns != $this->schema ? $this->escapeId($ns) . "." : "") .
            $this->table($foreignKey["table"]) . " (" . implode(", ", array_map(function ($idf) {
                return $this->escapeId($idf);
            }, $foreignKey["target"])) . ")" . //! reuse $name - check in older MySQL versions
            (preg_match("~^($this->onActions)\$~", $foreignKey["on_delete"]) ? " ON DELETE $foreignKey[on_delete]" : "") .
            (preg_match("~^($this->onActions)\$~", $foreignKey["on_update"]) ? " ON UPDATE $foreignKey[on_update]" : "")
        ;
    }

    /**
     * @inheritDoc
     */
    public function minVersion($version, $maria_db = "", $connection = null)
    {
        if (!$connection) {
            $connection = $this->connection;
        }
        $info = $connection->getServerInfo();
        if ($maria_db && preg_match('~([\d.]+)-MariaDB~', $info, $match)) {
            $info = $match[1];
            $version = $maria_db;
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
    public function quote($string)
    {
        return $this->connection->quote($string);
    }
}
