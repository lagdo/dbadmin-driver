<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Lagdo\DbAdmin\Driver\Entity\UserEntity;

interface ServerInterface
{
    /**
     * Get logged user
     *
     * @return string
     */
    public function user();

    /**
     * Get the users and hosts
     *
     * @param string $database  The database name
     *
     * @return array
     */
    public function getUsers(string $database): array;

    /**
     * Get the grants of a user on a given host
     *
     * @param string $user      The username
     * @param string $host      The host name
     *
     * @return UserEntity
     */
    public function getUserGrants(string $user, string $host): UserEntity;

    /**
     * Get the user privileges
     *
     * @param UserEntity $user
     *
     * @return void
     */
    public function getUserPrivileges(UserEntity $user);

    /**
     * Get cached list of databases
     *
     * @param bool $flush
     *
     * @return array
     */
    public function databases(bool $flush);

    /**
     * Compute size of database
     *
     * @param string $database
     *
     * @return int
     */
    public function databaseSize(string $database);

    /**
     * Get database collation
     *
     * @param string $database
     * @param array $collations
     *
     * @return string
     */
    public function databaseCollation(string $database, array $collations);

    /**
     * Get supported engines
     *
     * @return array
     */
    public function engines();

    /**
     * Get sorted grouped list of collations
     *
     * @return array
     */
    public function collations();

    /**
     * Find out if database is information_schema
     *
     * @param string $database
     *
     * @return bool
     */
    public function isInformationSchema(string $database);

    /**
     * Find out if database is a system database
     *
     * @param string $database
     *
     * @return bool
     */
    public function isSystemSchema(string $database);

    /**
     * Create a database
     *
     * @param string $database
     * @param string $collation
     *
     * @return boolean
     */
    public function createDatabase(string $database, string $collation) ;

    /**
     * Drop a database
     *
     * @param string $database
     *
     * @return bool
     */
    public function dropDatabase(string $database);

    /**
     * Rename database from DB
     *
     * @param string $name New name
     * @param string $collation
     *
     * @return bool
     */
    public function renameDatabase(string $name, string $collation);

    /**
     * Get list of available routine languages
     *
     * @return array
     */
    public function routineLanguages() ;

    /**
     * Get server variables
     *
     * @return array
     */
    public function variables();

    /**
     * Get status variables
     *
     * @return array
     */
    public function statusVariables();

    /**
     * Get process list
     *
     * @return array
     */
    public function processes();

    /**
     * Get a process attribute
     *
     * @param array $process
     * @param string $key
     * @param string $val
     *
     * @return string
     */
    public function processAttr(array $process, string $key, string $val): string;

    /**
     * Kill a process
     *
     * @param int
     *
     * @return bool
     */
    // public function killProcess($val);

    /**
     * Get maximum number of connections
     *
     * @return int
     */
    // public function maxConnections();
}
