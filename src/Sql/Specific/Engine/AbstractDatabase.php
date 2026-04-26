<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Engine;

use Lagdo\DbAdmin\Driver\Sql\AbstractDbProxy;
use Lagdo\DbAdmin\Driver\Sql\Dto\RoutineInfoDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\TableFieldDto;

abstract class AbstractDatabase extends AbstractDbProxy implements DatabaseInterface
{
    /**
     * @inheritDoc
     */
    public function createDatabase(string $database, string $collation): bool
    {
        // Note: The SQLite driver overrides this function.
        $query = $this->_statement()->getCreateDatabaseQuery($database, $collation);
        return $this->_engine()->execute($query) !== false;
    }

    /**
     * @inheritDoc
     */
    public function dropDatabase(string $database): bool
    {
        // Note: The SQLite driver overrides this function.
        // Cannot drop the connected database.
        if ($this->_engine()->database() === $database) {
            return false;
        }

        $query = $this->_statement()->getDropDatabaseQuery($database);
        return $this->_engine()->execute($query) !== false;
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
     * Find out if a database is a user database
     *
     * @param string $database
     *
     * @return bool
     */
    public function isUserSchema(string $database): bool
    {
        return !$this->isSystemSchema($database) || !$this->isInformationSchema($database);
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
