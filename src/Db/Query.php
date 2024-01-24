<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Entity\TableSelectEntity;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\UtilInterface;
use Lagdo\DbAdmin\Driver\TranslatorInterface;

use function implode;
use function array_keys;

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
    public function schema()
    {
        return '';
    }

    /**
     * Formulate SQL modification query with limit 1
     *
     * @param string $table
     * @param string $query Everything after UPDATE or DELETE
     * @param string $where
     *
     * @return string
     */
    abstract protected function limitToOne(string $table, string $query, string $where);

    /**
     * @inheritDoc
     */
    public function select(string $table, array $select, array $where,
        array $group, array $order = [], int $limit = 1, int $page = 0)
    {
        $entity = new TableSelectEntity($table, $select, $where, $group, $order, $limit, $page);
        $query = $this->driver->buildSelectQuery($entity);
        // $this->start = intval(microtime(true));
        return $this->driver->execute($query);
    }

    /**
     * @inheritDoc
     */
    public function insert(string $table, array $values)
    {
        $table = $this->driver->table($table);
        if (empty($values)) {
            $result = $this->driver->execute("INSERT INTO $table DEFAULT VALUES");
            return $result !== false;
        }
        $result = $this->driver->execute("INSERT INTO $table (" .
            implode(', ', array_keys($values)) . ') VALUES (' . implode(', ', $values) . ')');
        return $result !== false;
    }

    /**
     * @inheritDoc
     */
    public function update(string $table, array $values, string $queryWhere, int $limit = 0)
    {
        $assignments = [];
        foreach ($values as $name => $value) {
            $assignments[] = "$name = $value";
        }
        $query = $this->driver->table($table) . ' SET ' . implode(', ', $assignments);
        if (!$limit) {
            $result = $this->driver->execute('UPDATE ' . $query . $queryWhere);
            return $result !== false;
        }
        $result = $this->driver->execute('UPDATE' . $this->limitToOne($table, $query, $queryWhere));
        return $result !== false;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $table, string $queryWhere, int $limit = 0)
    {
        $query = 'FROM ' . $this->driver->table($table);
        if (!$limit) {
            $result = $this->driver->execute("DELETE $query $queryWhere");
            return $result !== false;
        }
        $result = $this->driver->execute('DELETE' . $this->limitToOne($table, $query, $queryWhere));
        return $result !== false;
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
