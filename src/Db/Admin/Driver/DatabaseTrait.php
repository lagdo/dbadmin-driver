<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Driver;

use Exception;

trait DatabaseTrait
{
    /**
     * @var DatabaseInterface
     */
    private DatabaseInterface $database;

    /**
     * @return DatabaseInterface
     */
    private function _d(): DatabaseInterface
    {
        return $this->database ??= new Database($this, $this->grammar(), $this->utils);
    }

    /**
     * Alter indexes
     *
     * @param string $table Escaped table name
     * @param array $alter  Indexes to alter. Array of IndexDto.
     * @param array $drop   Indexes to drop. Array of IndexDto.
     *
     * @return bool
     */
    public function alterIndexes(string $table, array $alter, array $drop): bool
    {
        return $this->_d()->alterIndexes($table, $alter, $drop);
    }

    /**
     * Drop views
     *
     * @param array $views
     *
     * @return bool
     */
    public function dropViews(array $views): bool
    {
        return $this->_d()->dropViews($views);
    }

    /**
     * Drop tables
     *
     * @param array $tables
     *
     * @return bool
     */
    public function dropTables(array $tables): bool
    {
        return $this->_d()->dropTables($tables);
    }

    /**
     * Truncate tables
     *
     * @param array $tables
     *
     * @return bool
     */
    public function truncateTables(array $tables): bool
    {
        return $this->_d()->truncateTables($tables);
    }

    /**
     * Create a view
     *
     * @param array $values The view values
     *
     * @return bool
     * @throws Exception
     */
    public function createView(array $values): bool
    {
        return $this->_d()->createView($values);
    }

    /**
     * Update a view
     *
     * @param string $view The view name
     * @param array $values The view values
     *
     * @return string
     * @throws Exception
     */
    public function updateView(string $view, array $values): string
    {
        return $this->_d()->updateView($view, $values);
    }

    /**
     * Drop a view
     *
     * @param string $view The view name
     *
     * @return bool
     * @throws Exception
     */
    public function dropView(string $view): bool
    {
        return $this->_d()->dropView($view);
    }
}
