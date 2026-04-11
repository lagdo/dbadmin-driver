<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Grammar;

interface DatabaseInterface
{
    /**
     * Check if utf8mb4 might be needed
     *
     * @param string $create
     *
     * @return void
     */
    public function setUtf8mb4(string $create): void;

    /**
     * Get SET NAMES query, if utf8mb4 might be needed
     *
     * @return string
     */
    public function getCharsetQuery(): string;

    /**
     * Command to update a view
     *
     * @param string $view The view name
     * @param array $values The view values
     *
     * @return array<string>
     */
    public function getUpdateViewQueries(string $view, array $values): array;

    /**
     * Command to drop a view
     *
     * @param string $view The view name
     *
     * @return string
     */
    public function getDropViewQuery(string $view): string;
}
