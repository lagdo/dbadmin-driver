<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;

use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\UtilInterface;
use Lagdo\DbAdmin\Driver\TranslatorInterface;
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
     * @var TranslatorInterface
     */
    protected $trans;

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
        $query = $this->driver->buildSelectQuery($table, $select, $where, $group, $order, $limit, $page);
        $start = microtime(true);
        return $this->connection->query($query);
    }

    /**
     * @inheritDoc
     */
    public function insert(string $table, array $values)
    {
        $table = $this->driver->table($table);
        if (!$values) {
            return $this->driver->queries("INSERT INTO $table DEFAULT VALUES");
        }
        return $this->driver->queries("INSERT INTO $table (" .
            implode(", ", array_keys($values)) . ") VALUES (" . implode(", ", $values) . ")");
    }

    /**
     * @inheritDoc
     */
    public function update(string $table, array $values, string $queryWhere, int $limit = 0, string $separator = "\n")
    {
        if (!$limit) {
            $assignments = [];
            foreach ($values as $name => $value) {
                $assignments[] = "$name = $value";
            }
            return $this->driver->queries("UPDATE " . $this->driver->table($table) .
                " SET$separator" . implode(",$separator", $assignments) . $queryWhere);
        }
        return $this->driver->queries("UPDATE" . $this->driver->limitToOne($table, $query, $queryWhere, $separator));
    }

    /**
     * @inheritDoc
     */
    public function delete(string $table, string $queryWhere, int $limit = 0)
    {
        $query = "FROM " . $this->driver->table($table);
        if (!$limit) {
            return $this->driver->queries("DELETE $query $queryWhere");
        }
        return $this->driver->queries("DELETE" . $this->driver->limitToOne($table, $query, $queryWhere));
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
            return array(implode("\n", $queries), $this->trans->formatTime($start));
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
        $values = [];
        $statement = $this->connection->query($query);
        if (is_object($statement)) {
            while ($row = $statement->fetchRow()) {
                $values[] = $row[$column];
            }
        }
        return $values;
    }

    /**
     * @inheritDoc
     */
    public function keyValues(string $query, ConnectionInterface $connection = null, bool $setKeys = true)
    {
        if (!is_object($connection)) {
            $connection = $this->connection;
        }
        $values = [];
        $statement = $connection->query($query);
        if (is_object($statement)) {
            while ($row = $statement->fetchRow()) {
                if ($setKeys) {
                    $values[$row[0]] = $row[1];
                } else {
                    $values[] = $row[0];
                }
            }
        }
        return $values;
    }

    /**
     * @inheritDoc
     */
    public function rows(string $query, ConnectionInterface $connection = null)
    {
        if (!$connection) {
            $connection = $this->connection;
        }
        $statement = $connection->query($query);
        if (!is_object($statement)) { // can return true
            return [];
        }
        $rows = [];
        while ($row = $statement->fetchAssoc()) {
            $rows[] = $row;
        }
        return $rows;
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
