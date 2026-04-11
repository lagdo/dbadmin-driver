<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Grammar;

trait DatabaseTrait
{
    /**
     * @var DatabaseInterface
     */
    private DatabaseInterface $database;

    /**
     * @return DatabaseInterface
     */
    private function _d(): DatabaseInterface
    {
        return $this->database ??= new Database($this->driver, $this, $this->utils);
    }

    /**
     * Check if utf8mb4 might be needed
     *
     * @param string $create
     *
     * @return void
     */
    public function setUtf8mb4(string $create): void
    {
        $this->_d()->setUtf8mb4($create);
    }

    /**
     * Get SET NAMES query, if utf8mb4 might be needed
     *
     * @return string
     */
    public function getCharsetQuery(): string
    {
        return $this->_d()->getCharsetQuery();
    }

    /**
     * Command to update a view
     *
     * @param string $view The view name
     * @param array $values The view values
     *
     * @return array<string>
     */
    public function getUpdateViewQueries(string $view, array $values): array
    {
        return $this->_d()->getUpdateViewQueries($view, $values);
    }

    /**
     * Command to drop a view
     *
     * @param string $view The view name
     *
     * @return string
     */
    public function getDropViewQuery(string $view): string
    {
        return $this->_d()->getDropViewQuery($view);
    }
}
