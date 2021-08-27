<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\AdminerDbInterface;
use Lagdo\DbAdmin\Driver\AdminerUtilInterface;

abstract class Driver implements DriverInterface
{
    /**
     * @var AdminerDbInterface
     */
    protected $db;

    /**
     * @var AdminerUtilInterface
     */
    protected $util;

    /**
     * @var ServerInterface
     */
    protected $server;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * The constructor
     *
     * @param AdminerDbInterface $db
     * @param AdminerUtilInterface $util
     * @param ServerInterface $server
     * @param ConnectionInterface $connection
     */
    public function __construct(AdminerDbInterface $db, AdminerUtilInterface $util,
        ServerInterface $server, ConnectionInterface $connection)
    {
        $this->db = $db;
        $this->util = $util;

        $this->server = $server;
        $this->connection = $connection;
    }

    /**
     * @inheritDoc
     */
    public function select($table, $select, $where, $group, $order = [], $limit = 1, $page = 0)
    {
        $is_group = (count($group) < count($select));
        $query = $this->db->buildSelectQuery($select, $where, $group, $order, $limit, $page);
        if (!$query) {
            $query = "SELECT" . $this->server->limit(
                ($page != "last" && $limit != "" && $group && $is_group && $this->server->jush == "sql" ?
                "SQL_CALC_FOUND_ROWS " : "") . implode(", ", $select) . "\nFROM " .
                $this->server->table($table),
                ($where ? "\nWHERE " . implode(" AND ", $where) : "") . ($group && $is_group ?
                "\nGROUP BY " . implode(", ", $group) : "") . ($order ? "\nORDER BY " .
                implode(", ", $order) : ""),
                ($limit != "" ? +$limit : null),
                ($page ? $limit * $page : 0),
                "\n"
            );
        }
        $start = microtime(true);
        $return = $this->connection->query($query);
        return $return;
    }

    /**
     * Delete data from table
     * @param string $table
     * @param string $queryWhere " WHERE ..."
     * @param int $limit 0 or 1
     * @return bool
     */
    public function delete($table, $queryWhere, $limit = 0)
    {
        $query = "FROM " . $this->server->table($table);
        return $this->db->queries("DELETE" .
            ($limit ? $this->server->limit1($table, $query, $queryWhere) : " $query$queryWhere"));
    }

    /**
     * Update data in table
     * @param string $table
     * @param array $set escaped columns in keys, quoted data in values
     * @param string $queryWhere " WHERE ..."
     * @param int $limit 0 or 1
     * @param string $separator
     * @return bool
     */
    public function update($table, $set, $queryWhere, $limit = 0, $separator = "\n")
    {
        $values = [];
        foreach ($set as $key => $val) {
            $values[] = "$key = $val";
        }
        $query = $this->server->table($table) . " SET$separator" . implode(",$separator", $values);
        return $this->db->queries("UPDATE" .
            ($limit ? $this->server->limit1($table, $query, $queryWhere, $separator) : " $query$queryWhere"));
    }

    /**
     * Insert data into table
     * @param string $table
     * @param array $set escaped columns in keys, quoted data in values
     * @return bool
     */
    public function insert($table, $set)
    {
        return $this->db->queries("INSERT INTO " . $this->server->table($table) . (
            $set
            ? " (" . implode(", ", array_keys($set)) . ")\nVALUES (" . implode(", ", $set) . ")"
            : " DEFAULT VALUES"
        ));
    }

    /**
     * Begin transaction
     * @return bool
     */
    public function begin()
    {
        return $this->db->queries("BEGIN");
    }

    /**
     * Commit transaction
     * @return bool
     */
    public function commit()
    {
        return $this->db->queries("COMMIT");
    }

    /**
     * Rollback transaction
     * @return bool
     */
    public function rollback()
    {
        return $this->db->queries("ROLLBACK");
    }

    /**
     * Return query with a timeout
     * @param string
     * @param int seconds
     * @return string or null if the driver doesn't support query timeouts
     */
    public function slowQuery($query, $timeout)
    {
    }

    /**
     * Convert column to be searchable
     * @param string $idf escaped column name
     * @param array $val array("op" => , "val" => )
     * @param array $field
     * @return string
     */
    public function convertSearch($idf, $val, $field)
    {
        return $idf;
    }

    /**
     * Quote binary string
     * @param string
     * @return string
     */
    public function quoteBinary($string)
    {
        return $this->connection->quote($string);
    }

    /**
     * Get warnings about the last command
     * @return string HTML
     */
    public function warnings()
    {
        return $this->connection->warnings();
    }

    /**
     * Get help link for table
     * @param string $name
     * @return string relative URL or null
     */
    public function tableHelp($name)
    {
        return '';
    }
}
