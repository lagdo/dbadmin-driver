<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Exception;
use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\UtilInterface;
use Lagdo\DbAdmin\Driver\TranslatorInterface;

use function trim;
use function strtoupper;
use function uniqid;

abstract class Database implements DatabaseInterface
{
    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var UtilInterface
     */
    protected $util;

    /**
     * @var TranslatorInterface
     */
    protected $trans;

    /**
     * The constructor
     *
     * @param DriverInterface $driver
     * @param UtilInterface $util
     * @param TranslatorInterface $trans
     */
    public function __construct(DriverInterface $driver, UtilInterface $util, TranslatorInterface $trans)
    {
        $this->driver = $driver;
        $this->util = $util;
        $this->trans = $trans;
    }

    /**
     * @inheritDoc
     */
    public function dropViews(array $views)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function moveTables(array $tables, array $views, string $target)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function copyTables(array $tables, array $views, string $target)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function truncateTables(array $tables)
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function createView(array $values)
    {
        // From view.inc.php
        $name = trim($values['name']);
        $type = $values['materialized'] ? ' MATERIALIZED VIEW ' : ' VIEW ';

        $sql = ($this->driver->jush() === 'mssql' ? 'ALTER' : 'CREATE OR REPLACE') .
            $type . $this->driver->table($name) . " AS\n" . $values['select'];
        return $this->driver->executeQuery($sql);
    }
    /**
     * Execute remembered queries
     *
     * @param bool $failed
     *
     * @return bool
     * @throws Exception
     */
    private function executeSavedQuery(bool $failed): bool
    {
        list($queries/*, $time*/) = $this->driver->queries();
        return $this->driver->executeQuery($queries, false, $failed/*, $time*/);
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
        $this->executeSavedQuery(!($this->driver->execute($test) &&
            $this->driver->execute($dropTest) &&
            $this->driver->execute($drop) && $this->driver->execute($create)));
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

        return $this->dropAndCreate("DROP $origType " . $this->driver->table($view),
            "CREATE $type " . $this->driver->table($name) . " AS\n" . $values['select'],
            "DROP $type " . $this->driver->table($name),
            "CREATE $type " . $this->driver->table($tempName) . " AS\n" . $values['select'],
            "DROP $type " . $this->driver->table($tempName), $view, $name);
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

        $sql = "DROP $origType " . $this->driver->table($view);
        return $this->driver->executeQuery($sql);
    }

    /**
     * @inheritDoc
     */
    public function sequences()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function userTypes()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function schemas()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function events()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function routine(string $name, string $type)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function routines()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function routineId(string $name, array $row)
    {
        return '';
    }
}
