<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Engine;

use Lagdo\DbAdmin\Driver\Sql\DbProxyTrait;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\UserDto;
use Exception;

use function count;
use function trim;

trait DatabaseTrait
{
    use DbProxyTrait;

    /**
     * Get the user privileges
     *
     * @param UserDto $user
     *
     * @return void
     */
    public function getUserPrivileges(UserDto $user): void
    {
        $user->privileges = $this->_engine()->rows('SHOW PRIVILEGES');
    }

    /**
     * Get status of a single table and fall back to name on error
     *
     * @param string $table
     * @param bool $fast Return only "Name", "Engine" and "Comment" columns
     *
     * @return TableDto
     */
    public function tableStatusOrName(string $table, bool $fast = false): TableDto
    {
        $status = $this->_engine()->tableStatus($table, $fast);
        return $status ?? new TableDto($table, $this->_engine()->columns(...));
    }

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
        $this->_engine()->execute('BEGIN');
        foreach ($queries as $query) {
            if (!$this->_engine()->execute($query)) {
                $this->_engine()->execute('ROLLBACK');
                return false;
            }
        }
        $this->_engine()->execute('COMMIT');
        return true;
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
        $queries = $this->_statement()->getTruncateTablesQueries($tables);
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
        $queries = $this->_statement()->getAlterIndexQueries($table, $alter, $drop);
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
        $command = $this->_engine()->mssql() ? 'ALTER' : 'CREATE OR REPLACE';
        $type = $values['materialized'] ? 'MATERIALIZED VIEW' : 'VIEW';
        $name = $this->_statement()->escapeTableName(trim($values['name']));

        $sql = "$command $type $name AS\n" . $values['select'];
        return $this->_engine()->execute($sql);
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
            $this->_engine()->executeQuery($dropOrig);
            return 'dropped';
        }
        if ($oldName === '') {
            $this->_engine()->executeQuery($createNew);
            return 'created';
        }
        if ($oldName !== $newName) {
            $created = $this->_engine()->execute($createNew);
            $dropped = $this->_engine()->execute($dropOrig);
            // $this->executeSavedQuery(!($created && $this->_engine()->execute($dropOrig)));
            if (!$dropped && $created) {
                $this->_engine()->execute($dropCreated);
            }
            return 'altered';
        }

        /*$this->executeSavedQuery(!($this->_engine()->execute($createTest) &&
            $this->_engine()->execute($dropTest) &&
            $this->_engine()->execute($dropOrig) &&
            $this->_engine()->execute($createNew)));*/
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
            $this->_statement()->getUpdateViewQueries($view, $values);
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
        return $this->_engine()->execute($this->_statement()->getDropViewQuery($view));
    }
}
