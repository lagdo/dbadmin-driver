<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Entity\TableSelectEntity;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;

use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\UtilInterface;
use Lagdo\DbAdmin\Driver\TranslatorInterface;
use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;

use function implode;
use function array_keys;
use function intval;
use function microtime;
use function is_object;
use function preg_replace;

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
     * Executed queries
     *
     * @var array
     */
    protected $queries = [];

    /**
     * Query start timestamp
     *
     * @var int
     */
    protected $start = 0;

    /**
     * The constructor
     *
     * @param DriverInterface $driver
     * @param UtilInterface $util
     * @param TranslatorInterface $trans
     * @param ConnectionInterface $connection
     */
    public function __construct(DriverInterface $driver, UtilInterface $util,
        TranslatorInterface $trans, ConnectionInterface $connection)
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
        $entity = new TableSelectEntity($table, $select, $where, $group, $order, $limit, $page);
        $query = $this->driver->buildSelectQuery($entity);
        $this->start = intval(microtime(true));
        return $this->connection->query($query);
    }

    /**
     * @inheritDoc
     */
    public function insert(string $table, array $values)
    {
        $table = $this->driver->table($table);
        if (!empty($values)) {
            $result = $this->execute("INSERT INTO $table DEFAULT VALUES");
            return $result == true;
        }
        $result = $this->execute("INSERT INTO $table (" .
            implode(", ", array_keys($values)) . ") VALUES (" . implode(", ", $values) . ")");
        return $result == true;
    }

    /**
     * @inheritDoc
     */
    public function update(string $table, array $values, string $queryWhere, int $limit = 0, string $separator = "\n")
    {
        $assignments = [];
        foreach ($values as $name => $value) {
            $assignments[] = "$name = $value";
        }
        $query = $this->driver->table($table) . " SET$separator" . implode(",$separator", $assignments);
        if (!$limit) {
            $result = $this->execute('UPDATE ' . $query . $queryWhere);
            return $result == true;
        }
        $result = $this->execute('UPDATE' . $this->driver->limitToOne($table, $query, $queryWhere, $separator));
        return $result == true;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $table, string $queryWhere, int $limit = 0)
    {
        $query = 'FROM ' . $this->driver->table($table);
        if (!$limit) {
            $result = $this->execute("DELETE $query $queryWhere");
            return $result == true;
        }
        $result = $this->execute('DELETE' . $this->driver->limitToOne($table, $query, $queryWhere));
        return $result == true;
    }

    /**
     * @inheritDoc
     */
    public function explain(ConnectionInterface $connection, string $query)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function slowQuery(string $query, int $timeout)
    {
        return null;
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
    public function execute(string $query)
    {
        if (!$this->start) {
            $this->start = intval(microtime(true));
        }
        $this->queries[] = (preg_match('~;$~', $query) ? "DELIMITER ;;\n$query;\nDELIMITER " : $query) . ";";
        return $this->connection->query($query);
    }

    /**
     * @inheritDoc
     */
    public function queries()
    {
        return [implode("\n", $this->queries), $this->trans->formatTime($this->start)];
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
            if (!$this->execute("$query " . $escape($table))) {
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
        $result = $this->connection->query("BEGIN");
        return $result == true;
    }

    /**
     * @inheritDoc
     */
    public function commit()
    {
        $result = $this->connection->query("COMMIT");
        return $result == true;
    }

    /**
     * @inheritDoc
     */
    public function rollback()
    {
        $result = $this->connection->query("ROLLBACK");
        return $result == true;
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
