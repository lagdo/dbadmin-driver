<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Lagdo\DbAdmin\Driver\Entity\PartitionEntity;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\Entity\TriggerEntity;

interface TableInterface
{
    /**
     * Get table status
     *
     * @param string $table
     * @param bool $fast Return only "Name", "Engine" and "Comment" fields
     *
     * @return TableEntity|null
     */
    public function tableStatus(string $table, bool $fast = false);

    /**
     * Get all tables statuses
     *
     * @param bool $fast Return only "Name", "Engine" and "Comment" fields
     *
     * @return TableEntity[]
     */
    public function tableStatuses(bool $fast = false);

    /**
     * Get all tables names
     *
     * @return array
     */
    public function tableNames();

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
     *
     * @return array
     */
    public function indexes(string $table);

    /**
     * Get foreign keys in table
     *
     * @param string $table
     *
     * @return array
     */
    public function foreignKeys(string $table);

    /**
     * Get defined check constraints
     *
     * @param TableEntity $status
     *
     * @return array
     */
    public function checkConstraints(TableEntity $status): array;

    /**
     * Get partitions info
     *
     * @param string $table
     *
     * @return PartitionEntity|null
     */
    public function partitionsInfo(string $table): PartitionEntity|null;

    /**
     * Get information about a trigger
     *
     * @param string $name
     * @param string $table
     *
     * @return TriggerEntity
     */
    public function trigger(string $name, string $table = '');

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
     * Get referencable tables with single column primary key except self
     *
     * @param string $table
     *
     * @return array
     */
    public function referencableTables(string $table);

    /**
     * Get help link for table
     *
     * @param string $name
     *
     * @return string relative URL or null
     */
    public function tableHelp(string $name);
}
