<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Statement;

use Lagdo\DbAdmin\Driver\Sql\Dto\TableAlterDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableCreateDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableDto;

trait TableTrait
{
    /**
     * @return AbstractTable
     */
    abstract protected function _table(): AbstractTable;

    /**
     * Get SQL commands to create a table
     *
     * @param TableCreateDto $table
     *
     * @return array<string>
     */
    public function getCreateTableQueries(TableCreateDto $table): array
    {
        return $this->_table()->getCreateTableQueries($table);
    }

    /**
     * Get SQL commands to alter a table
     *
     * @param TableAlterDto $table
     *
     * @return array<string>
     */
    public function getAlterTableQueries(TableAlterDto $table): array
    {
        return $this->_table()->getAlterTableQueries($table);
    }

    /**
     * Get SQL command to create table
     *
     * @param string $table
     * @param bool $autoIncrement
     * @param string $style
     *
     * @return string
     */
    public function getExportTableQueries(string $table, bool $autoIncrement, string $style): string
    {
        return $this->_table()->getExportTableQueries($table, $autoIncrement, $style);
    }

    /**
     * Get SQL command to create foreign keys
     *
     * getExportTableQueries() produces CREATE TABLE without FK CONSTRAINTs
     * getForeignKeyQueries() produces all FK CONSTRAINTs as ALTER TABLE ... ADD CONSTRAINT
     * so that all FKs can be added after all tables have been created, avoiding any need
     * to reorder CREATE TABLE statements in order of their FK dependencies
     *
     * @param TableDto $table
     *
     * @return array
     */
    public function getForeignKeyQueries(TableDto $table): array
    {
        return $this->_table()->getForeignKeyQueries($table);
    }

    /**
     * Command to create an index
     *
     * @param string $table
     * @param string $type
     * @param string $name
     * @param string $columns
     *
     * @return string
     */
    public function getCreateIndexQuery(string $table, string $type, string $name, string $columns): string
    {
        return $this->_table()->getCreateIndexQuery($table, $type, $name, $columns);
    }

    /**
     * Command to alter indexes
     *
     * @param string $table Escaped table name
     * @param array $alter<IndexDto>  Indexes to alter
     * @param array $drop<IndexDto>   Indexes to drop
     *
     * @return array<string>
     */
    public function getAlterIndexQueries(string $table, array $alter, array $drop): array
    {
        return $this->_table()->getAlterIndexQueries($table, $alter, $drop);
    }

    /**
     * Get SQL command to truncate table
     *
     * @param string $table
     *
     * @return string
     */
    public function getTruncateTableQuery(string $table): string
    {
        return $this->_table()->getTruncateTableQuery($table);
    }

    /**
     * Get SQL commands to create triggers
     *
     * @param string $table
     *
     * @return string
     */
    public function getCreateTriggerQuery(string $table): string
    {
        return $this->_table()->getCreateTriggerQuery($table);
    }
}
