<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\Entity\IndexEntity;
use Lagdo\DbAdmin\Driver\Entity\ForeignKeyEntity;
use Lagdo\DbAdmin\Driver\Entity\TriggerEntity;
use Lagdo\DbAdmin\Driver\Entity\RoutineEntity;

use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;

interface TableInterface
{
    /**
     * Get the name of the primary id field
     *
     * @return string
     */
    public function primaryIdName();

    /**
     * Get table status
     *
     * @param string $table
     * @param bool $fast Return only "Name", "Engine" and "Comment" fields
     *
     * @return TableEntity
     */
    public function tableStatus(string $table = "", bool $fast = false);

    /**
     * Get status of a single table and fall back to name on error
     *
     * @param string $table
     * @param bool $fast Return only "Name", "Engine" and "Comment" fields
     *
     * @return TableEntity
     */
    public function tableStatusOrName(string $table, bool $fast = false);

    /**
     * Find out whether the identifier is view
     *
     * @param TableEntity $tableStatus
     *
     * @return bool
     */
    public function isView(TableEntity $tableStatus);

    /**
     * Check if table supports foreign keys
     *
     * @param TableEntity $tableStatus
     *
     * @return bool
     */
    public function supportForeignKeys(TableEntity $tableStatus);

    /**
     * Get information about fields
     *
     * @param string $table
     *
     * @return array
     */
    public function fields(string $table);

    /**
     * Get table indexes
     *
     * @param string $table
     * @param ConnectionInterface  $connection
     *
     * @return array
     */
    public function indexes(string $table, ConnectionInterface $connection = null);

    /**
     * Get foreign keys in table
     *
     * @param string $table
     *
     * @return array array($name => array("db" => , "ns" => , "table" => , "source" => [], "target" => [], "onDelete" => , "onUpdate" => ))
     */
    public function foreignKeys(string $table);

    /**
     * Get information about a trigger
     *
     * @param string $trigger
     *
     * @return TriggerEntity
     */
    public function trigger(string $trigger);

    /**
     * Get defined triggers
     *
     * @param string $table
     *
     * @return array
     */
    public function triggers(string $table);

    /**
     * Get trigger options
     *
     * @return array ("Timing" => [], "Event" => [], "Type" => [])
     */
    public function triggerOptions();

    /**
     * Find backward keys for table
     *
     * @param string
     * @param string
     *
     * @return array $return[$target_table]["keys"][$key_name][$target_column] = $source_column; $return[$target_table]["name"] = $this->tableName($target_table);
     */
    public function backwardKeys(string $table, string $tableName);

    /**
     * Get descriptions of selected data
     *
     * @param array $rows All data to print
     * @param array $foreignKeys
     *
     * @return array
     */
    public function rowDescriptions(array $rows, array $foreignKeys);

    /**
     * Find out foreign keys for each column
     *
     * @param string $table
     *
     * @return array
     */
    public function columnForeignKeys(string $table);

    /**
     * Get referencable tables with single column primary key except self
     *
     * @param string $table
     *
     * @return array
     */
    public function referencableTables(string $table);

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
    public function alterTable(string $table, string $name, array $fields, array $foreign, string $comment,
        string $engine, string $collation, int $autoIncrement, string $partitioning);

    /**
     * Alter indexes
     *
     * @param string $table     Escaped table name
     * @param array $alter      array("index type", "name", array("column definition", ...)) or array("index type", "name", "DROP")
     *
     * @return bool
     */
    public function alterIndexes(string $table, array $alter);

    /**
     * Get help link for table
     *
     * @param string $name
     *
     * @return string relative URL or null
     */
    public function tableHelp(string $name);
}
