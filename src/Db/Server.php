<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;
use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\Driver\ServerInterface;
use Lagdo\DbAdmin\Driver\Entity\UserEntity;
use Lagdo\DbAdmin\Driver\Utils\Utils;

use function preg_match;
use function preg_match_all;

abstract class Server implements ServerInterface
{
    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * The constructor
     *
     * @param DriverInterface $driver
     * @param Utils $utils
     */
    public function __construct(protected DriverInterface $driver, protected Utils $utils)
    {}

    /**
     * @param ConnectionInterface $connection
     *
     * @return void
     */
    public function setConnection(ConnectionInterface $connection): void
    {
        $this->connection = $connection;
    }

    /**
     * @inheritDoc
     */
    public function getUsers(string $database): array
    {
        // From privileges.inc.php
        $clause = ($database == '' ? 'user' : 'db WHERE ' .
            $this->connection->quote($database) . ' LIKE Db');
        $query = "SELECT User, Host FROM mysql.$clause ORDER BY Host, User";
        $statement = $this->connection->query($query);
        // $grant = $statement;
        if (!$statement) {
            // list logged user, information_schema.USER_PRIVILEGES lists just the current user too
            $statement = $this->connection->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) " .
                "AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");
        }
        $users = [];
        while ($user = $statement->fetchAssoc()) {
            $users[] = $user;
        }
        return $users;
    }

    /**
     * @param UserEntity $user
     * @param array $grant
     *
     * @return void
     */
    private function addUserGrant(UserEntity $user, array $grant)
    {
        if (preg_match('~GRANT (.*) ON (.*) TO ~', $grant[0], $match) &&
            preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~', $match[1], $matches, PREG_SET_ORDER)) {
            //! escape the part between ON and TO
            foreach ($matches as $val) {
                $match2 = $match[2] ?? '';
                $val2 = $val[2] ?? '';
                if ($val[1] != 'USAGE') {
                    $user->grants["$match2$val2"][$val[1]] = true;
                }
                if (preg_match('~ WITH GRANT OPTION~', $grant[0])) { //! don't check inside strings and identifiers
                    $user->grants["$match2$val2"]['GRANT OPTION'] = true;
                }
            }
        }
        if (preg_match("~ IDENTIFIED BY PASSWORD '([^']+)~", $grant[0], $match)) {
            $user->password = $match[1];
        }
    }

    /**
     * @inheritDoc
     */
    public function getUserGrants(string $user, string $host): UserEntity
    {
        $entity = new UserEntity($user, $host);

        // From user.inc.php
        //! use information_schema for MySQL 5 - column names in column privileges are not escaped
        $query = 'SHOW GRANTS FOR ' . $this->connection->quote($user) .
            '@' . $this->connection->quote($host);
        if (!($statement = $this->connection->query($query))) {
            return $entity;
        }

        while ($grant = $statement->fetchRow()) {
            $this->addUserGrant($entity, $grant);
        }
        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function getUserPrivileges(UserEntity $user): void
    {
        $user->privileges = $this->driver->rows('SHOW PRIVILEGES');
    }

    /**
     * @inheritDoc
     */
    public function engines(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function collations(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function databaseCollation(string $database, array $collations): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function isInformationSchema(string $database): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function isSystemSchema(string $database): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function variables(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function statusVariables(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function routineLanguages(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function renameDatabase(string $name, string $collation): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function processes(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function processAttr(array $process, string $key, string $val): string
    {
        return $this->utils->str->html($val);
    }
}
