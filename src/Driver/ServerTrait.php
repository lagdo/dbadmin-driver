<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Lagdo\DbAdmin\Driver\Db\Server;
use Lagdo\DbAdmin\Driver\Entity\UserEntity;

trait ServerTrait
{
    /**
     * @var Server
     */
    protected $server;

    /**
     * Get the users and hosts
     *
     * @param string $database  The database name
     *
     * @return array
     */
    public function getUsers(string $database): array
    {
        return $this->server->getUsers($database);
    }

    /**
     * Get the grants of a user on a given host
     *
     * @param string $user      The username
     * @param string $host      The host name
     *
     * @return UserEntity
     */
    public function getUserGrants(string $user, string $host): UserEntity
    {
        return $this->server->getUserGrants($user, $host);
    }

    /**
     * Get the user privileges
     *
     * @param UserEntity $user
     *
     * @return void
     */
    public function getUserPrivileges(UserEntity $user)
    {
        $this->server->getUserPrivileges($user);
    }

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
     * Find out if database is a system database
     *
     * @param string $database
     *
     * @return bool
     */
    public function isSystemSchema(string $database)
    {
        return $this->server->isSystemSchema($database);
    }

    /**
     * Create a database
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
     * Drop a database
     *
     * @param string $database
     *
     * @return bool
     */
    public function dropDatabase(string $database)
    {
        return $this->server->dropDatabase($database);
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
     * Get a process name
     *
     * @param array $process
     * @param string $key
     * @param string $val
     *
     * @return string
     */
    public function processAttr(array $process, string $key, string $val): string
    {
        return $this->server->processAttr($process, $key, $val);
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
