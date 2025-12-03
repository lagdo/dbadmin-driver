<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Lagdo\DbAdmin\Driver\Entity\UserEntity;

trait ServerTrait
{
    /**
     * @var ServerInterface
     */
    abstract protected function _server(): ServerInterface;

    /**
     * Get logged user
     *
     * @return string
     */
    public function user(): string
    {
        return $this->_server()->user();
    }

    /**
     * Get current schema from the database
     *
     * @return string
     */
    // public function schema()
    // {
    //     return $this->_server()->schema();
    // }

    /**
     * Get the users and hosts
     *
     * @param string $database  The database name
     *
     * @return array
     */
    public function getUsers(string $database): array
    {
        return $this->_server()->getUsers($database);
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
        return $this->_server()->getUserGrants($user, $host);
    }

    /**
     * Get the user privileges
     *
     * @param UserEntity $user
     *
     * @return void
     */
    public function getUserPrivileges(UserEntity $user): void
    {
        $this->_server()->getUserPrivileges($user);
    }

    /**
     * Get cached list of databases
     *
     * @param bool $flush
     *
     * @return array
     */
    public function databases(bool $flush): array
    {
        return $this->_server()->databases($flush);
    }

    /**
     * Compute size of database
     *
     * @param string $database
     *
     * @return int
     */
    public function databaseSize(string $database): int
    {
        return $this->_server()->databaseSize($database);
    }

    /**
     * Get database collation
     *
     * @param string $database
     * @param array $collations
     *
     * @return string
     */
    public function databaseCollation(string $database, array $collations): string
    {
        return $this->_server()->databaseCollation($database, $collations);
    }

    /**
     * Get supported engines
     *
     * @return array
     */
    public function engines(): array
    {
        return $this->_server()->engines();
    }

    /**
     * Get sorted grouped list of collations
     *
     * @return array
     */
    public function collations(): array
    {
        return $this->_server()->collations();
    }

    /**
     * Find out if database is information_schema
     *
     * @param string $database
     *
     * @return bool
     */
    public function isInformationSchema(string $database): bool
    {
        return $this->_server()->isInformationSchema($database);
    }

    /**
     * Find out if database is a system database
     *
     * @param string $database
     *
     * @return bool
     */
    public function isSystemSchema(string $database): bool
    {
        return $this->_server()->isSystemSchema($database);
    }

    /**
     * Create a database
     *
     * @param string $database
     * @param string $collation
     *
     * @return string|boolean
     */
    public function createDatabase(string $database, string $collation): bool
    {
        return $this->_server()->createDatabase($database, $collation);
    }

    /**
     * Drop a database
     *
     * @param string $database
     *
     * @return bool
     */
    public function dropDatabase(string $database): bool
    {
        return $this->_server()->dropDatabase($database);
    }

    /**
     * Rename database from DB
     *
     * @param string $name New name
     * @param string $collation
     *
     * @return bool
     */
    public function renameDatabase(string $name, string $collation): bool
    {
        return $this->_server()->renameDatabase($name, $collation);
    }

    /**
     * Get list of available routine languages
     *
     * @return array
     */
    public function routineLanguages(): array
    {
        return $this->_server()->routineLanguages();
    }

    /**
     * Get server variables
     *
     * @return array
     */
    public function variables(): array
    {
        return $this->_server()->variables();
    }

    /**
     * Get status variables
     *
     * @return array
     */
    public function statusVariables(): array
    {
        return $this->_server()->statusVariables();
    }

    /**
     * Get process list
     *
     * @return array
     */
    public function processes(): array
    {
        return $this->_server()->processes();
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
        return $this->_server()->processAttr($process, $key, $val);
    }

    /**
     * Kill a process
     *
     * @param int
     *
     * @return bool
     */
    // public function killProcess($val): bool
    // {
    //     return $this->_server()->killProcess($val);
    // }

    /**
     * Get maximum number of connections
     *
     * @return int
     */
    // public function maxConnections(): int
    // {
    //     return $this->_server()->maxConnections();
    // }
}
