<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Statement;

trait DatabaseTrait
{
    /**
     * @return AbstractDatabase
     */
    abstract protected function _database(): AbstractDatabase;

    /**
     * Get SQL command to change database
     *
     * @param string $database
     * @param string $style
     *
     * @return string
     */
    public function getUseDatabaseQuery(string $database, string $style = ''): string
    {
        return $this->_database()->getUseDatabaseQuery($database, $style);
    }

    /**
     * Create a database
     *
     * @param string $database
     * @param string $collation
     *
     * @return string
     */
    public function getCreateDatabaseQuery(string $database, string $collation): string
    {
        return $this->_database()->getCreateDatabaseQuery($database, $collation);
    }

    /**
     * Drop a database
     *
     * @param string $database
     *
     * @return string
     */
    public function getDropDatabaseQuery(string $database): string
    {
        return $this->_database()->getDropDatabaseQuery($database);
    }

    /**
     * Command to drop views
     *
     * @param array $views
     *
     * @return array<string>
     */
    public function getDropViewsQueries(array $views): array
    {
        return $this->_database()->getDropViewsQueries($views);
    }

    /**
     * Command to drop tables
     *
     * @param array $tables
     *
     * @return array<string>
     */
    public function getDropTablesQueries(array $tables): array
    {
        return $this->_database()->getDropTablesQueries($tables);
    }

    /**
     * Command to truncate tables
     *
     * @param array $tables
     *
     * @return array<string>
     */
    public function getTruncateTablesQueries(array $tables): array
    {
        return $this->_database()->getTruncateTablesQueries($tables);
    }
}
