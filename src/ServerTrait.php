<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Entity\RoutineEntity;

trait ServerTrait
{
    /**
     * Get cached list of databases
     *
     * @param bool $flush
     *
     * @return array
     */
    public function databases(bool $flush)
    {
        return $this->server->databases($flush);
    }

    /**
     * Compute size of database
     *
     * @param string $database
     *
     * @return int
     */
    public function databaseSize(string $database)
    {
        return $this->server->databaseSize($database);
    }

    /**
     * Get database collation
     *
     * @param string $database
     * @param array $collations
     *
     * @return string
     */
    public function databaseCollation(string $database, array $collations)
    {
        return $this->server->databaseCollation($database, $collations);
    }

    /**
     * Get supported engines
     *
     * @return array
     */
    public function engines()
    {
        return $this->server->engines();
    }

    /**
     * Get sorted grouped list of collations
     *
     * @return array
     */
    public function collations()
    {
        return $this->server->collations();
    }

    /**
     * Find out if database is information_schema
     *
     * @param string $database
     *
     * @return bool
     */
    public function isInformationSchema(string $database)
    {
        return $this->server->isInformationSchema($database);
    }

    /**
     * Create database
     *
     * @param string $database
     * @param string $collation
     *
     * @return string|boolean
     */
    public function createDatabase(string $database, string $collation)
    {
        return $this->server->createDatabase($database, $collation);
    }

    /**
     * Drop databases
     *
     * @param array $databases
     *
     * @return bool
     */
    public function dropDatabases(array $databases)
    {
        return $this->server->dropDatabases($databases);
    }

    /**
     * Rename database from DB
     *
     * @param string $name New name
     * @param string $collation
     *
     * @return bool
     */
    public function renameDatabase(string $name, string $collation)
    {
        return $this->server->renameDatabase($name, $collation);
    }

    /**
     * Get list of available routine languages
     *
     * @return array
     */
    public function routineLanguages()
    {
        return $this->server->routineLanguages();
    }

    /**
     * Get routine signature
     *
     * @param string $name
     * @param array $row result of routine()
     *
     * @return string
     */
    public function routineId(string $name, array $row)
    {
        return $this->server->routineId($name, $row);
    }

    /**
     * Get server variables
     *
     * @return array
     */
    public function variables()
    {
        return $this->server->variables();
    }

    /**
     * Get status variables
     *
     * @return array
     */
    public function statusVariables()
    {
        return $this->server->statusVariables();
    }

    /**
     * Get process list
     *
     * @return array
     */
    public function processes()
    {
        return $this->server->processes();
    }

    /**
     * Kill a process
     *
     * @param int
     *
     * @return bool
     */
    // public function killProcess($val)
    // {
    //     return $this->server->killProcess($val);
    // }

    /**
     * Get maximum number of connections
     *
     * @return int
     */
    // public function maxConnections()
    // {
    //     return $this->server->maxConnections();
    // }
}
