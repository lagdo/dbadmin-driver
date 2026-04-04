<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Grammar;

trait DatabaseTrait
{
    /**
     * @return DatabaseInterface
     */
    abstract protected function _database(): DatabaseInterface;

    /**
     * @inheritDoc
     */
    public function setUtf8mb4(string $create): void
    {
        $this->_database()->setUtf8mb4($create);
    }

    /**
     * @inheritDoc
     */
    public function getCharsetQuery(): string
    {
        return $this->_database()->getCharsetQuery();
    }

    /**
     * @inheritDoc
     */
    public function getUseDatabaseQuery(string $database, string $style = ''): string
    {
        return $this->_database()->getUseDatabaseQuery($database, $style);
    }

    /**
     * @inheritDoc
     */
    public function getCreateDatabaseQuery(string $database, string $collation): string
    {
        return $this->_database()->getCreateDatabaseQuery($database, $collation);
    }

    /**
     * @inheritDoc
     */
    public function getDropDatabaseQuery(string $database): string
    {
        return $this->_database()->getDropDatabaseQuery($database);
    }

    /**
     * @inheritDoc
     */
    public function getAutoIncrementModifier(): string
    {
        return $this->_database()->getAutoIncrementModifier();
    }

    /**
     * @inheritDoc
     */
    public function getDropViewsQueries(array $views): array
    {
        return $this->_database()->getDropViewsQueries($views);
    }

    /**
     * @inheritDoc
     */
    public function getDropTablesQueries(array $tables): array
    {
        return $this->_database()->getDropTablesQueries($tables);
    }

    /**
     * @inheritDoc
     */
    public function getTruncateTablesQueries(array $tables): array
    {
        return $this->_database()->getTruncateTablesQueries($tables);
    }
}
