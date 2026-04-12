<?php

namespace Lagdo\DbAdmin\Support\Db\Engine\Driver;

use Lagdo\DbAdmin\Support\Db\AbstractDbProxy;
use Lagdo\DbAdmin\Support\Dto\RoutineInfoDto;
use Lagdo\DbAdmin\Support\Dto\TableFieldDto;

abstract class AbstractDatabase extends AbstractDbProxy implements DatabaseInterface
{
    /**
     * @inheritDoc
     */
    public function createDatabase(string $database, string $collation): bool
    {
        // Note: The SQLite driver overrides this function.
        $query = $this->_grammar()->getCreateDatabaseQuery($database, $collation);
        return $this->_driver()->execute($query) !== false;
    }

    /**
     * @inheritDoc
     */
    public function dropDatabase(string $database): bool
    {
        // Note: The SQLite driver overrides this function.
        // Cannot drop the connected database.
        if ($this->_driver()->database() === $database) {
            return false;
        }
        $query = $this->_grammar()->getDropDatabaseQuery($database);
        return $this->_driver()->execute($query) !== false;
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
