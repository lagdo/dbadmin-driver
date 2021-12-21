<?php

namespace Lagdo\DbAdmin\Driver;

interface InputInterface
{
    /**
     * Get the query table/trigger name
     *
     * @return string
     */
    public function getTable(): string;

    /**
     * Return true if a table was defined
     *
     * @return bool
     */
    public function hasTable(): bool;

    /**
     * Get the select query fields
     *
     * @return array
     */
    public function getSelect(): array;

    /**
     * Get the query filters
     *
     * @return array
     */
    public function getWhere(): array;

    /**
     * Get the query limit
     *
     * @return int
     */
    public function getLimit(): int;

    /**
     * Get the query fields
     *
     * @return array
     */
    public function getFields(): array;

    /**
     * Get the auto increment step
     *
     * @return string
     */
    public function getAutoIncrementStep(): string;

    /**
     * Get the auto increment field
     *
     * @return string
     */
    public function getAutoIncrementField(): string;

    /**
     * @return array
     */
    public function getChecks(): array;

    /**
     * @return bool
     */
    public function getOverwrite(): bool;
}
