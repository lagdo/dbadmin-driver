<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Exception;
use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\Driver\DatabaseInterface;
use Lagdo\DbAdmin\Driver\Entity\RoutineInfoEntity;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Utils\Utils;

use function strtoupper;
use function trim;
use function uniqid;

abstract class AbstractDatabase implements DatabaseInterface
{
    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var Utils
     */
    protected $utils;

    /**
     * The constructor
     *
     * @param DriverInterface $driver
     * @param Utils $utils
     */
    public function __construct(DriverInterface $driver, Utils $utils)
    {
        $this->driver = $driver;
        $this->utils = $utils;
    }

    /**
     * @inheritDoc
     */
    public function dropViews(array $views): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function moveTables(array $tables, array $views, string $target): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function copyTables(array $tables, array $views, string $target): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function truncateTables(array $tables): bool
    {
        return false;
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
            $type . $this->driver->escapeTableName($name) . " AS\n" . $values['select'];
        return $this->driver->executeQuery($sql);
    }

    /**
     * Drop old object and create a new one
     *
     * @param string $drop Drop old object query
     * @param string $create Create new object query
     * @param string $dropCreated Drop new object query
     * @param string $test Create test object query
     * @param string $dropTest Drop test object query
     * @param string $oldName
     * @param string $newName
     *
     * @return string
     * @throws Exception
     */
    private function dropAndCreate(string $drop, string $create, string $dropCreated,
        string $test, string $dropTest, string $oldName, string $newName): string
    {
        if ($oldName == '' && $newName == '') {
            $this->driver->executeQuery($drop);
            return 'dropped';
        }
        if ($oldName == '') {
            $this->driver->executeQuery($create);
            return 'created';
        }
        if ($oldName != $newName) {
            $created = $this->driver->execute($create);
            $dropped = $this->driver->execute($drop);
            // $this->executeSavedQuery(!($created && $this->driver->execute($drop)));
            if (!$dropped && $created) {
                $this->driver->execute($dropCreated);
            }
            return 'altered';
        }
        /*$this->executeSavedQuery(!($this->driver->execute($test) &&
            $this->driver->execute($dropTest) &&
            $this->driver->execute($drop) && $this->driver->execute($create)));*/
        return 'altered';
    }

    /**
     * @inheritDoc
     */
    public function updateView(string $view, array $values): string
    {
        // From view.inc.php
        $origType = 'VIEW';
        if ($this->driver->jush() === 'pgsql') {
            $status = $this->driver->tableStatus($view);
            $origType = strtoupper($status->engine);
        }

        $name = trim($values['name']);
        $type = $values['materialized'] ? 'MATERIALIZED VIEW' : 'VIEW';
        $tempName = $name . '_adminer_' . uniqid();

        return $this->dropAndCreate("DROP $origType " . $this->driver->escapeTableName($view),
            "CREATE $type " . $this->driver->escapeTableName($name) . " AS\n" . $values['select'],
            "DROP $type " . $this->driver->escapeTableName($name),
            "CREATE $type " . $this->driver->escapeTableName($tempName) . " AS\n" . $values['select'],
            "DROP $type " . $this->driver->escapeTableName($tempName), $view, $name);
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
        // From view.inc.php
        $origType = 'VIEW';
        if ($this->driver->jush() == 'pgsql') {
            $status = $this->driver->tableStatus($view);
            $origType = strtoupper($status->engine);
        }

        $sql = "DROP $origType " . $this->driver->escapeTableName($view);
        return $this->driver->executeQuery($sql);
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
    public function routine(string $name, string $type): RoutineInfoEntity|null
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
    public function enumValues(TableFieldEntity $field): array
    {
        return [];
    }
}
