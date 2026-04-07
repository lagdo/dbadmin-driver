<?php

namespace Lagdo\DbAdmin\Support\Db\Engine\Driver;

use Lagdo\DbAdmin\Support\Db\Admin\Driver\DatabaseInterface;
use Lagdo\DbAdmin\Support\DriverInterface;
use Lagdo\DbAdmin\Support\Dto\RoutineInfoDto;
use Lagdo\DbAdmin\Support\Dto\TableFieldDto;
use Lagdo\DbAdmin\Support\GrammarInterface;
use Lagdo\DbAdmin\Support\Utils\Utils;
use Exception;

use function count;
use function trim;

abstract class AbstractDatabase implements DatabaseInterface
{
    /**
     * @param DriverInterface $driver
     * @param GrammarInterface $grammar
     * @param Utils $utils
     */
    public function __construct(protected DriverInterface $driver,
        protected GrammarInterface $grammar, protected Utils $utils)
    {}

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
        $this->driver->execute('BEGIN');
        foreach ($queries as $query) {
            if (!$this->driver->execute($query)) {
                $this->driver->execute('ROLLBACK');
                return false;
            }
        }
        $this->driver->execute('COMMIT');
        return true;
    }

    /**
     * @inheritDoc
     */
    public function dropViews(array $views): bool
    {
        $queries = $this->grammar->getDropViewsQueries($views);
        return count($queries) > 0 ? $this->executeTransaction($queries) : false;
    }

    /**
     * @inheritDoc
     */
    public function dropTables(array $tables): bool
    {
        $queries = $this->grammar->getDropTablesQueries($tables);
        return count($queries) > 0 ? $this->executeTransaction($queries) : false;
    }

    /**
     * @inheritDoc
     */
    public function truncateTables(array $tables): bool
    {
        $queries = $this->grammar->getTruncateTablesQueries($tables);
        return count($queries) > 0 ? $this->executeTransaction($queries) : false;
    }

    /**
     * @inheritDoc
     */
    public function alterIndexes(string $table, array $alter, array $drop): bool
    {
        $queries = $this->grammar->getAlterIndexQueries($table, $alter, $drop);
        return count($queries) > 0 ? $this->executeTransaction($queries) : false;
    }

    /**
     * @inheritDoc
     */
    public function createView(array $values): bool
    {
        // From view.inc.php
        $name = trim($values['name']);
        $type = $values['materialized'] ? ' MATERIALIZED VIEW ' : ' VIEW ';

        $sql = ($this->driver->jush() === 'mssql' ? 'ALTER' : 'CREATE OR REPLACE') .
            $type . $this->grammar->escapeTableName($name) . " AS\n" . $values['select'];
        return $this->driver->executeQuery($sql);
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
            $this->driver->executeQuery($dropOrig);
            return 'dropped';
        }
        if ($oldName === '') {
            $this->driver->executeQuery($createNew);
            return 'created';
        }
        if ($oldName !== $newName) {
            $created = $this->driver->execute($createNew);
            $dropped = $this->driver->execute($dropOrig);
            // $this->executeSavedQuery(!($created && $this->driver->execute($dropOrig)));
            if (!$dropped && $created) {
                $this->driver->execute($dropCreated);
            }
            return 'altered';
        }

        /*$this->executeSavedQuery(!($this->driver->execute($createTest) &&
            $this->driver->execute($dropTest) &&
            $this->driver->execute($dropOrig) &&
            $this->driver->execute($createNew)));*/
        return 'altered';
    }

    /**
     * @inheritDoc
     */
    public function updateView(string $view, array $values): string
    {
        [$dropOrig, $createNew, $dropCreated, $createTest, $dropTest] =
            $this->grammar->getUpdateViewQueries($view, $values);
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
        return $this->driver->executeQuery($this->grammar->getDropViewQuery($view));
    }

    /**
     * @inheritDoc
     */
    public function sequences(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function userTypes(bool $withValues): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function schemas(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function events(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function routine(string $name, string $type): RoutineInfoDto|null
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function routines(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function routineId(string $name, array $row): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function enumValues(TableFieldDto $field): array
    {
        return [];
    }
}
