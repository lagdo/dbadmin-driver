<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\DbInterface;
use Lagdo\DbAdmin\Driver\UtilInterface;

abstract class Server implements ServerInterface
{
    /**
     * @var DbInterface
     */
    protected $db;

    /**
     * @var UtilInterface
     */
    protected $util;

    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

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
    public $on_actions = "RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT"; ///< @var string used in foreign_keys()

    /**
     * From index.php
     * @var string
     */
    public $enum_length = "'(?:''|[^'\\\\]|\\\\.)*'";

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
        $config = $this->driver_config();
        $this->possible_drivers = $config['possible_drivers'];
        $this->jush = $config['jush'];
        $this->types = $config['types'];
        $this->structured_types = $config['structured_types'];
        $this->unsigned = $config['unsigned'];
        $this->operators = $config['operators'];
        $this->functions = $config['functions'];
        $this->grouping = $config['grouping'];
        $this->edit_functions = $config['edit_functions'];

        $this->createConnection();
    }

    /**
     * Create a connection to the server, based on the config and available packages
     *
     * @return void
     */
    abstract protected function createConnection();

    /**
     * @inheritDoc
     */
    public function getDriver()
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
            $this->connection->select_db($database);
            if ($schema !== '') {
                $this->set_schema($schema);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function current_db()
    {
        return $this->database;
    }

    /**
     * @inheritDoc
     */
    public function current_schema()
    {
        return $this->schema;
    }

    /**
     * @inheritDoc
     */
    public function primary()
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function idf_escape($idf)
    {
        return $idf;
    }

    /**
     * @inheritDoc
     */
    public function idf_unescape($idf)
    {
        $last = substr($idf, -1);
        return str_replace($last . $last, $last, substr($idf, 1, -1));
    }

    /**
     * @inheritDoc
     */
    public function table($idf)
    {
        return $this->idf_escape($idf);
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
    public function db_collation($db, $collations)
    {
        return "";
    }

    /**
     * @inheritDoc
     */
    public function user_types()
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
    public function get_schema()
    {
        return "";
    }

    /**
     * @inheritDoc
     */
    public function set_schema($schema, $connection = null)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function information_schema($db)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function is_view($table_status)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function foreign_keys($table)
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function fk_support($table_status)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function convert_field(array $field)
    {
    }

    /**
     * @inheritDoc
     */
    public function unconvert_field(array $field, $return)
    {
        return $return;
    }

    /**
     * @inheritDoc
     */
    public function show_variables()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function show_status()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function process_list()
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
    public function create_sql($table, $auto_increment, $style)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function move_tables($tables, $views, $target)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function copy_tables($tables, $views, $target)
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
    public function trigger_options()
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
    public function routine_languages()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function routine_id($name, $row)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function use_sql($database)
    {
        return "";
    }

    /**
     * @inheritDoc
     */
    public function foreign_keys_sql($table)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function truncate_sql($table)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function trigger_sql($table)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function last_id()
    {
        return $this->connection->last_id;
    }

    /**
     * @inheritDoc
     */
    public function found_rows($table_status, $where)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function rename_database($name, $collation)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function auto_increment()
    {
        return "";
    }

    /**
     * @inheritDoc
     */
    public function alter_indexes($table, $alter)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function drop_views($views)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function truncate_tables($tables)
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
    public function limit1($table, $query, $where, $separator = "\n")
    {
        return $this->limit($query, $where, 1, 0, $separator);
    }

    /**
     * @inheritDoc
     */
    public function table_status1($table, $fast = false)
    {
        $return = $this->table_status($table, $fast);
        return ($return ? $return : array("Name" => $table));
    }

    /**
     * @inheritDoc
     */
    public function format_foreign_key($foreign_key)
    {
        $db = $foreign_key["db"];
        $ns = $foreign_key["ns"];
        return " FOREIGN KEY (" . implode(", ", array_map(function ($idf) {
            return $this->idf_escape($idf);
        }, $foreign_key["source"])) . ") REFERENCES " .
            ($db != "" && $db != $this->database ? $this->idf_escape($db) . "." : "") .
            ($ns != "" && $ns != $this->schema ? $this->idf_escape($ns) . "." : "") .
            $this->table($foreign_key["table"]) . " (" . implode(", ", array_map(function ($idf) {
                return $this->idf_escape($idf);
            }, $foreign_key["target"])) . ")" . //! reuse $name - check in older MySQL versions
            (preg_match("~^($this->on_actions)\$~", $foreign_key["on_delete"]) ? " ON DELETE $foreign_key[on_delete]" : "") .
            (preg_match("~^($this->on_actions)\$~", $foreign_key["on_update"]) ? " ON UPDATE $foreign_key[on_update]" : "")
        ;
    }

    /**
     * @inheritDoc
     */
    public function min_version($version, $maria_db = "", $connection = null)
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
        return ($this->min_version("5.5.3", 0) ? "utf8mb4" : "utf8");
    }

    /**
     * @inheritDoc
     */
    public function q($string)
    {
        return $this->connection->quote($string);
    }
}
