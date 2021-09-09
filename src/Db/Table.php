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

abstract class Table implements TableInterface
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
    public function primaryIdName()
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function tableStatusOrName(string $table, bool $fast = false)
    {
        if (($status = $this->tableStatus($table, $fast))) {
            return $status;
        }
        return new TableEntity($table);
    }

    /**
     * @inheritDoc
     */
    public function foreignKeys(string $table)
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function supportForeignKeys(TableEntity $tableStatus)
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
    public function alterIndexes(string $table, array $alte)
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
    public function isView(TableEntity $tableStatus)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function trigger(string $trigger)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function triggers(string $table)
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
    public function backwardKeys(string $table, string $tableName)
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function rowDescriptions(array $rows, array $foreignKeys)
    {
        return $rows;
    }

    /**
     * @inheritDoc
     */
    public function columnForeignKeys(string $table)
    {
        $keys = [];
        foreach ($this->foreignKeys($table) as $foreignKey) {
            foreach ($foreignKey->source as $val) {
                $keys[$val][] = $foreignKey;
            }
        }
        return $keys;
    }

    /**
     * @inheritDoc
     */
    public function referencableTables(string $table)
    {
        $fields = []; // table_name => field
        foreach ($this->tableStatus('', true) as $tableName => $tableStatus) {
            if ($tableName != $table && $this->supportForeignKeys($tableStatus)) {
                foreach ($this->fields($tableName) as $field) {
                    if ($field->primary) {
                        if (isset($fields[$tableName])) { // multi column primary key
                            unset($fields[$tableName]);
                            break;
                        }
                        $fields[$tableName] = $field;
                    }
                }
            }
        }
        return $fields;
    }

    /**
     * @inheritDoc
     */
    public function tableHelp(string $name)
    {
        return '';
    }
}
