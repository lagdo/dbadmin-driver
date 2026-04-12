<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Engine;

use Lagdo\DbAdmin\Driver\Sql\Dto\PartitionDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableFieldDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TriggerDto;

trait TableTrait
{
    /**
     * @return AbstractTable
     */
    abstract protected function _table(): AbstractTable;

    /**
     * Get table status
     *
     * @param string $table
     * @param bool $fast Return only "Name", "Engine" and "Comment" fields
     *
     * @return TableDto|null
     */
    public function tableStatus(string $table, bool $fast = false): TableDto|null
    {
        return $this->_table()->tableStatus($table, $fast);
    }

    /**
     * Get all tables statuses
     *
     * @param bool $fast Return only "Name", "Engine" and "Comment" fields
     *
     * @return TableDto[]
     */
    public function tableStatuses(bool $fast = false): array
    {
        return $this->_table()->tableStatuses($fast);
    }

    /**
     * Get all tables names
     *
     * @return array
     */
    public function tableNames(): array
    {
        return $this->_table()->tableNames();
    }

    /**
     * Find out whether the identifier is view
     *
     * @param TableDto $tableStatus
     *
     * @return bool
     */
    public function isView(TableDto $tableStatus): bool
    {
        return $this->_table()->isView($tableStatus);
    }

    /**
     * Check if table supports foreign keys
     *
     * @param TableDto $tableStatus
     *
     * @return bool
     */
    public function supportForeignKeys(TableDto $tableStatus): bool
    {
        return $this->_table()->supportForeignKeys($tableStatus);
    }

    /**
     * Get information about fields
     *
     * @param string $table
     *
     * @return array<TableFieldDto>
     */
    public function fields(string $table): array
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
    public function indexes(string $table): array
    {
        return $this->_table()->indexes($table);
    }

    /**
     * Get foreign keys in table
     *
     * @param string $table
     *
     * @return array
     */
    public function foreignKeys(string $table): array
    {
        return $this->_table()->foreignKeys($table);
    }

    /**
     * Get defined check constraints
     *
     * @param TableDto $status
     *
     * @return array
     */
    public function checkConstraints(TableDto $status): array
    {
        return $this->_table()->checkConstraints($status);
    }

    /**
     * Get partitions info
     *
     * @param string $table
     *
     * @return PartitionDto|null
     */
    public function partitionsInfo(string $table): PartitionDto|null
    {
        return $this->_table()->partitionsInfo($table);
    }

    /**
     * Get information about a trigger
     *
     * @param string $name
     * @param string $table
     *
     * @return TriggerDto
     */
    public function trigger(string $name, string $table = ''): TriggerDto|null
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
    public function triggers(string $table): array
    {
        return $this->_table()->triggers($table);
    }

    /**
     * Get trigger options
     *
     * @return array ("Timing" => [], "Event" => [], "Type" => [])
     */
    public function triggerOptions(): array
    {
        return $this->_table()->triggerOptions();
    }

    /**
     * Get help link for table
     *
     * @param string $name
     *
     * @return string relative URL or null
     */
    public function tableHelp(string $name): string
    {
        return $this->_table()->tableHelp($name);
    }
}
