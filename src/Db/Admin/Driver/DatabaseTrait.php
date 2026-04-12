<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Driver;

use Lagdo\DbAdmin\Support\Db\DbProxyTrait;
use Exception;

use function count;
use function trim;

trait DatabaseTrait
{
    use DbProxyTrait;

    /**
     * @param array $queries
     *
     * @return bool
     */
    private function executeTransaction(array $queries): bool
    {
        if (!$queries) {
            return false;
        }
        $this->_driver()->execute('BEGIN');
        foreach ($queries as $query) {
            if (!$this->_driver()->execute($query)) {
                $this->_driver()->execute('ROLLBACK');
                return false;
            }
        }
        $this->_driver()->execute('COMMIT');
        return true;
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
        $queries = $this->_grammar()->getDropViewsQueries($views);
        return count($queries) > 0 ? $this->executeTransaction($queries) : false;
    }

    /**
     * @inheritDoc
     */
    public function dropTables(array $tables): bool
    {
        $queries = $this->_grammar()->getDropTablesQueries($tables);
        return count($queries) > 0 ? $this->executeTransaction($queries) : false;
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
        $queries = $this->_grammar()->getTruncateTablesQueries($tables);
        return count($queries) > 0 ? $this->executeTransaction($queries) : false;
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
        $queries = $this->_grammar()->getAlterIndexQueries($table, $alter, $drop);
        return count($queries) > 0 ? $this->executeTransaction($queries) : false;
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
        // From view.inc.php
        $name = trim($values['name']);
        $type = $values['materialized'] ? ' MATERIALIZED VIEW ' : ' VIEW ';

        $sql = ($this->_driver()->mssql() ? 'ALTER' : 'CREATE OR REPLACE') .
            $type . $this->_grammar()->escapeTableName($name) . " AS\n" . $values['select'];
        return $this->_driver()->executeQuery($sql);
    }

    /**
     * Drop old object and create a new one
     *
     * @param string $dropOrig Drop old object query
     * @param string $createNew Create new object query
     * @param string $dropCreated Drop new object query
     * @param string $createTest Create test object query
     * @param string $dropTest Drop test object query
     * @param string $oldName
     * @param string $newName
     *
     * @return string
     * @throws Exception
     */
    private function dropAndCreate(string $dropOrig, string $createNew, string $dropCreated,
        string $createTest, string $dropTest, string $oldName, string $newName): string
    {
        if ($oldName === '' && $newName === '') {
            $this->_driver()->executeQuery($dropOrig);
            return 'dropped';
        }
        if ($oldName === '') {
            $this->_driver()->executeQuery($createNew);
            return 'created';
        }
        if ($oldName !== $newName) {
            $created = $this->_driver()->execute($createNew);
            $dropped = $this->_driver()->execute($dropOrig);
            // $this->executeSavedQuery(!($created && $this->_driver()->execute($dropOrig)));
            if (!$dropped && $created) {
                $this->_driver()->execute($dropCreated);
            }
            return 'altered';
        }

        /*$this->executeSavedQuery(!($this->_driver()->execute($createTest) &&
            $this->_driver()->execute($dropTest) &&
            $this->_driver()->execute($dropOrig) &&
            $this->_driver()->execute($createNew)));*/
        return 'altered';
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
        [$dropOrig, $createNew, $dropCreated, $createTest, $dropTest] =
            $this->_grammar()->getUpdateViewQueries($view, $values);
        return $this->dropAndCreate($dropOrig, $createNew, $dropCreated,
            $createTest, $dropTest, $view, trim($values['name']));
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
        return $this->_driver()->executeQuery($this->_grammar()->getDropViewQuery($view));
    }
}
