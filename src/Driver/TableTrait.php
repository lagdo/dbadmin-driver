<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Lagdo\DbAdmin\Driver\Entity\PartitionEntity;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\Entity\TriggerEntity;

trait TableTrait
{
    /**
     * @var TableInterface
     */
    abstract protected function _table(): TableInterface;

    /**
     * Get table status
     *
     * @param string $table
     * @param bool $fast Return only "Name", "Engine" and "Comment" fields
     *
     * @return TableEntity|null
     */
    public function tableStatus(string $table, bool $fast = false)
    {
        return $this->_table()->tableStatus($table, $fast);
    }

    /**
     * Get all tables statuses
     *
     * @param bool $fast Return only "Name", "Engine" and "Comment" fields
     *
     * @return TableEntity[]
     */
    public function tableStatuses(bool $fast = false)
    {
        return $this->_table()->tableStatuses($fast);
    }

    /**
     * Get all tables names
     *
     * @return array
     */
    public function tableNames()
    {
        return $this->_table()->tableNames();
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
        return $this->_table()->tableStatusOrName($table, $fast);
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
        return $this->_table()->isView($tableStatus);
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
        return $this->_table()->supportForeignKeys($tableStatus);
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
        return $this->_table()->fields($table);
    }

    /**
     * Get table indexes
     *
     * @param string $table
     *
     * @return array
     */
    public function indexes(string $table)
    {
        return $this->_table()->indexes($table);
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
        return $this->_table()->foreignKeys($table);
    }

    /**
     * Get defined check constraints
     *
     * @param TableEntity $status
     *
     * @return array
     */
    public function checkConstraints(TableEntity $status): array
    {
        return $this->_table()->checkConstraints($status);
    }

    /**
     * Get partitions info
     *
     * @param string $table
     *
     * @return PartitionEntity|null
     */
    public function partitionsInfo(string $table): PartitionEntity|null
    {
        return $this->_table()->partitionsInfo($table);
    }

    /**
     * Get information about a trigger
     *
     * @param string $name
     * @param string $table
     *
     * @return TriggerEntity
     */
    public function trigger(string $name, string $table = '')
    {
        return $this->_table()->trigger($name, $table);
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
        return $this->_table()->triggers($table);
    }

    /**
     * Get trigger options
     *
     * @return array ("Timing" => [], "Event" => [], "Type" => [])
     */
    public function triggerOptions()
    {
        return $this->_table()->triggerOptions();
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
        return $this->_table()->referencableTables($table);
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
        return $this->_table()->tableHelp($name);
    }
}
