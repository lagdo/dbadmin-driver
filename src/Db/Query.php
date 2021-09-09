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

abstract class Query implements QueryInterface
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
     * The last error code
     *
     * @var int
     */
    protected $errno = 0;

    /**
     * The last error message
     *
     * @var string
     */
    protected $error = '';

    /**
     * The number of rows affected by the last query
     *
     * @var int
     */
    protected $affectedRows;

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
    public function schema()
    {
        return "";
    }

    /**
     * @inheritDoc
     */
    public function select(string $table, array $select, array $where,
        array $group, array $order = [], int $limit = 1, int $page = 0)
    {
        $is_group = (count($group) < count($select));
        $query = $this->driver->buildSelectQuery($select, $where, $group, $order, $limit, $page);
        if (!$query) {
            $query = "SELECT" . $this->driver->limit(
                ($page != "last" && $limit != "" && $group && $is_group && $this->driver->jush() == "sql" ?
                "SQL_CALC_FOUND_ROWS " : "") . implode(", ", $select) . "\nFROM " .
                $this->driver->table($table),
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
     * @inheritDoc
     */
    public function insert(string $table, array $set)
    {
        return $this->driver->queries("INSERT INTO " . $this->driver->table($table) .
            $set ? " (" . implode(", ", array_keys($set)) . ")\nVALUES (" . implode(", ", $set) . ")" :
            " DEFAULT VALUES");
    }

    /**
     * @inheritDoc
     */
    public function update(string $table, array $set, string $queryWhere, int $limit = 0, string $separator = "\n")
    {
        $values = [];
        foreach ($set as $key => $val) {
            $values[] = "$key = $val";
        }
        $query = $this->driver->table($table) . " SET$separator" . implode(",$separator", $values);
        return $this->driver->queries("UPDATE" . $limit ?
            $this->driver->limitToOne($table, $query, $queryWhere, $separator) : " $query$queryWhere");
    }

    /**
     * @inheritDoc
     */
    public function delete(string $table, string $queryWhere, int $limit = 0)
    {
        $query = "FROM " . $this->driver->table($table);
        return $this->driver->queries("DELETE" .
            ($limit ? $this->driver->limitToOne($table, $query, $queryWhere) : " $query$queryWhere"));
    }

    /**
     * @inheritDoc
     */
    public function explain(ConnectionInterface $connection, string $query)
    {
        return null;
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
    public function slowQuery(string $query, int $timeout)
    {
    }

    /**
     * @inheritDoc
     */
    public function setError(string $error = '')
    {
        $this->error = $error;
    }

    /**
     * @inheritDoc
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * @inheritDoc
     */
    public function hasError()
    {
        return $this->error !== '';
    }

    /**
     * @inheritDoc
     */
    public function setErrno($errno)
    {
        $this->errno = $errno;
    }

    /**
     * @inheritDoc
     */
    public function errno()
    {
        return $this->errno;
    }

    /**
     * @inheritDoc
     */
    public function hasErrno()
    {
        return $this->errno !== 0;
    }

    /**
     * @inheritDoc
     */
    public function setAffectedRows($affectedRows)
    {
        $this->affectedRows = $affectedRows;
    }

    /**
     * @inheritDoc
     */
    public function affectedRows()
    {
        return $this->affectedRows;
    }

    /**
     * @inheritDoc
     */
    public function queries(string $query)
    {
        static $queries = [];
        static $start;
        if (!$start) {
            $start = microtime(true);
        }
        if ($query === null) {
            // return executed queries
            return array(implode("\n", $queries), $this->util->formatTime($start));
        }
        $queries[] = (preg_match('~;$~', $query) ? "DELIMITER ;;\n$query;\nDELIMITER " : $query) . ";";
        return $this->connection->query($query);
    }

    /**
     * @inheritDoc
     */
    public function applyQueries(string $query, array $tables, $escape = null)
    {
        if (!$escape) {
            $escape = function ($table) {
                return $this->driver->table($table);
            };
        }
        foreach ($tables as $table) {
            if (!$this->queries("$query " . $escape($table))) {
                return false;
            }
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function values(string $query, $column = 0)
    {
        $return = [];
        $statement = $this->connection->query($query);
        if (is_object($statement)) {
            while ($row = $statement->fetchRow()) {
                $return[] = $row[$column];
            }
        }
        return $return;
    }

    /**
     * @inheritDoc
     */
    public function keyValues(string $query, ConnectionInterface $connection = null, bool $setKeys = true)
    {
        if (!is_object($connection)) {
            $connection = $this->connection;
        }
        $return = [];
        $statement = $connection->query($query);
        if (is_object($statement)) {
            while ($row = $statement->fetchRow()) {
                if ($setKeys) {
                    $return[$row[0]] = $row[1];
                } else {
                    $return[] = $row[0];
                }
            }
        }
        return $return;
    }

    /**
     * @inheritDoc
     */
    public function rows(string $query, ConnectionInterface $connection = null)
    {
        if (!is_object($connection)) {
            $connection = $this->connection;
        }
        $return = [];
        $statement = $connection->query($query);
        if (is_object($statement)) { // can return true
            while ($row = $statement->fetchAssoc()) {
                $return[] = $row;
            }
        }
        return $return;
    }

    /**
     * @inheritDoc
     */
    public function removeDefiner(string $query)
    {
        return preg_replace('~^([A-Z =]+) DEFINER=`' .
            preg_replace('~@(.*)~', '`@`(%|\1)', $this->user()) .
            '`~', '\1', $query); //! proper escaping of user
    }

    /**
     * @inheritDoc
     */
    public function begin()
    {
        return $this->queries("BEGIN");
    }

    /**
     * @inheritDoc
     */
    public function commit()
    {
        return $this->queries("COMMIT");
    }

    /**
     * @inheritDoc
     */
    public function rollback()
    {
        return $this->queries("ROLLBACK");
    }

    /**
     * @inheritDoc
     */
    public function countRows(TableEntity $tableStatus, array $where)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function convertSearch(string $idf, array $val, TableFieldEntity $field)
    {
        return $idf;
    }

    /**
     * @inheritDoc
     */
    public function view(string $name)
    {
        return [];
    }
}
