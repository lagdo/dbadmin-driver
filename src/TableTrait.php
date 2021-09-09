<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Entity\ConfigEntity;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\Entity\ForeignKeyEntity;
use Lagdo\DbAdmin\Driver\Entity\TriggerEntity;
use Lagdo\DbAdmin\Driver\Entity\RoutineEntity;

use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;

trait TableTrait
{
    /**
     * Get the name of the primary id field
     *
     * @return string
     */
    public function primaryIdName()
    {
        return $this->table->primaryIdName();
    }

    /**
     * Get table status
     *
     * @param string $table
     * @param bool $fast Return only "Name", "Engine" and "Comment" fields
     *
     * @return TableEntity
     */
    public function tableStatus(string $table = "", bool $fast = false)
    {
        return $this->table->tableStatus($table, $fast);
    }

    /**
     * Get status of a single table and fall back to name on error
     *
     * @param string $table
     * @param bool $fast Return only "Name", "Engine" and "Comment" fields
     *
     * @return TableEntity
     */
    public function tableStatusOrName(string $table, bool $fast = false)
    {
        return $this->table->tableStatusOrName($table, $fast);
    }

    /**
     * Find out whether the identifier is view
     *
     * @param TableEntity $tableStatus
     *
     * @return bool
     */
    public function isView(TableEntity $tableStatus)
    {
        return $this->table->isView($tableStatus);
    }

    /**
     * Check if table supports foreign keys
     *
     * @param TableEntity $tableStatus
     *
     * @return bool
     */
    public function supportForeignKeys(TableEntity $tableStatus)
    {
        return $this->table->supportForeignKeys($tableStatus);
    }

    /**
     * Get information about fields
     *
     * @param string $table
     *
     * @return array
     */
    public function fields(string $table)
    {
        return $this->table->fields($table);
    }

    /**
     * Get table indexes
     *
     * @param string $table
     * @param ConnectionInterface $connection
     *
     * @return array
     */
    public function indexes(string $table, ConnectionInterface $connection = null)
    {
        return $this->table->indexes($table, $connection);
    }

    /**
     * Get foreign keys in table
     *
     * @param string $table
     *
     * @return array array($name => array("db" => , "ns" => , "table" => , "source" => [], "target" => [], "onDelete" => , "onUpdate" => ))
     */
    public function foreignKeys(string $table)
    {
        return $this->table->foreignKeys($table);
    }

    /**
     * Create or alter table
     *
     * @param string $table "" to create
     * @param string $name new name
     * @param array $fields of array($orig, $process_field, $after)
     * @param array $foreign of strings
     * @param string $comment
     * @param string $engine
     * @param string $collation
     * @param int $autoIncrement number
     * @param string $partitioning
     *
     * @return bool
     */
    public function alterTable(string $table, string $name, array $fields, array $foreign,
        string $comment, string $engine, string $collation, int $autoIncrement, string $partitioning)
    {
        return $this->table->alterTable($table, $name, $fields, $foreign, $comment,
            $engine, $collation, $autoIncrement, $partitioning);
    }

    /**
     * Alter indexes
     *
     * @param string $table     Escaped table name
     * @param array $alter      array("index type", "name", array("column definition", ...)) or array("index type", "name", "DROP")
     *
     * @return bool
     */
    public function alterIndexes(string $table, array $alter)
    {
        return $this->table->alterIndexes($table, $alter);
    }

    /**
     * Get information about a trigger
     *
     * @param string $trigger
     *
     * @return TriggerEntity
     */
    public function trigger(string $trigger)
    {
        return $this->table->trigger($trigger);
    }

    /**
     * Get defined triggers
     *
     * @param string $table
     *
     * @return array
     */
    public function triggers(string $table)
    {
        return $this->table->triggers($table);
    }

    /**
     * Get trigger options
     *
     * @return array ("Timing" => [], "Event" => [], "Type" => [])
     */
    public function triggerOptions()
    {
        return $this->table->triggerOptions();
    }

    /**
     * Drop views
     *
     * @param array $views
     *
     * @return bool
     */
    public function dropViews(array $views)
    {
        return $this->table->dropViews($views);
    }

    /**
     * Truncate tables
     *
     * @param array $tables
     *
     * @return bool
     */
    public function truncateTables(array $tables)
    {
        return $this->table->truncateTables($tables);
    }

    /**
     * Drop tables
     *
     * @param array $tables
     *
     * @return bool
     */
    public function dropTables(array $tables)
    {
        return $this->table->dropTables($tables);
    }

    /**
     * Move tables to other schema
     *
     * @param array $tables
     * @param array $views
     * @param string $target
     *
     * @return bool
     */
    public function moveTables(array $tables, array $views, string $target)
    {
        return $this->table->moveTables($tables, $views, $target);
    }

    /**
     * Copy tables to other schema
     *
     * @param array $tables
     * @param array $views
     * @param string $target
     *
     * @return bool
     */
    public function copyTables(array $tables, array $views, string $target)
    {
        return $this->table->copyTables($tables, $views, $target);
    }

    /**
     * Find backward keys for table
     *
     * @param string $table
     * @param string $tableName
     *
     * @return array $return[$target_table]["keys"][$key_name][$target_column] = $source_column; $return[$target_table]["name"] = return $this->tableName($target_table);
     */
    public function backwardKeys(string $table, string $tableName)
    {
        return $this->table->backwardKeys($table, $tableName);
    }

    /**
     * Get descriptions of selected data
     *
     * @param array $rows All data to print
     * @param array $foreignKeys
     *
     * @return array
     */
    public function rowDescriptions(array $rows, array $foreignKeys)
    {
        return $this->table->rowDescriptions($rows, $foreignKeys);
    }

    /**
     * Find out foreign keys for each column
     *
     * @param string $table
     *
     * @return array
     */
    public function columnForeignKeys(string $table)
    {
        return $this->table->columnForeignKeys($table);
    }

    /**
     * Get referencable tables with single column primary key except self
     *
     * @param string $table
     *
     * @return array
     */
    public function referencableTables(string $table)
    {
        return $this->table->referencableTables($table);
    }

    /**
     * Get help link for table
     *
     * @param string $name
     *
     * @return string relative URL or null
     */
    public function tableHelp(string $name)
    {
        return $this->table->tableHelp($name);
    }
}
