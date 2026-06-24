<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Engine;

use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\ForeignKeyDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\IndexDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\PartitionDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TriggerDto;

interface TableInterface
{
    /**
     * Get table status
     *
     * @param string $table
     * @param bool $fast Return only "Name", "Engine" and "Comment" columns
     *
     * @return TableDto|null
     */
    public function tableStatus(string $table, bool $fast = false): TableDto|null;

    /**
     * Get all tables statuses
     *
     * @param bool $fast Return only "Name", "Engine" and "Comment" columns
     *
     * @return TableDto[]
     */
    public function tableStatuses(bool $fast = false): array;

    /**
     * Get all tables names
     *
     * @return array
     */
    public function tableNames(): array;

    /**
     * Find out whether the identifier is a view
     *
     * @param TableDto $tableStatus
     *
     * @return bool
     */
    public function isView(TableDto $tableStatus): bool;

    /**
     * Find out whether the identifier is a table (not a view)
     *
     * @param TableDto $tableStatus
     *
     * @return bool
     */
    public function isTable(TableDto $tableStatus): bool;

    /**
     * Check if table supports foreign keys
     *
     * @param TableDto $tableStatus
     *
     * @return bool
     */
    public function supportForeignKeys(TableDto $tableStatus): bool;

    /**
     * Get information about columns
     *
     * @param string|TableDto $table
     *
     * @return array<ColumnDto>
     */
    public function columns(string|TableDto $table): array;

    /**
     * Get table indexes
     *
     * @param string $table
     *
     * @return array<IndexDto>
     */
    public function indexes(string $table): array;

    /**
     * Get foreign keys in table
     *
     * @param string $table
     *
     * @return array<ForeignKeyDto>
     */
    public function foreignKeys(string $table): array;

    /**
     * Get defined check constraints
     *
     * @param TableDto $status
     *
     * @return array
     */
    public function checkConstraints(TableDto $status): array;

    /**
     * Get partitions info
     *
     * @param string $table
     *
     * @return PartitionDto|null
     */
    public function partitionsInfo(string $table): PartitionDto|null;

    /**
     * Get information about a trigger
     *
     * @param string $name
     * @param string $table
     *
     * @return TriggerDto|null
     */
    public function trigger(string $name, string $table): TriggerDto|null;

    /**
     * Get defined triggers
     *
     * @param string $table
     *
     * @return array<TriggerDto>
     */
    public function triggers(string $table): array;

    /**
     * Get trigger options
     *
     * @return array ("Timing" => [], "Event" => [], "Type" => [])
     */
    public function triggerOptions(): array;

    /**
     * Get help link for table
     *
     * @param string $name
     *
     * @return string relative URL or null
     */
    public function tableHelp(string $name): string;
}
