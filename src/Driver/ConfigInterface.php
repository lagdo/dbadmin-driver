<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;

interface ConfigInterface
{
    /**
     * Get the server jush
     *
     * @return string
     */
    public function jush(): string;

    /**
     * Get the Adminer version
     *
     * @return string
     */
    public function version(): string;

    /**
     * Check if a feature is supported
     *
     * @param string $feature
     *
     * @return bool
     */
    public function support(string $feature): bool;

    /**
     * @return array
     */
    public function unsigned(): array;

    /**
     * @return array
     */
    public function functions(): array;

    /**
     * @return array
     */
    public function grouping(): array;

    /**
     * @return array
     */
    public function operators(): array;

    /**
     * @return array
     */
    public function insertFunctions(): array;

    /**
     * @return array
     */
    public function editFunctions(): array;

    /**
     * @return array
     */
    public function structuredTypes(): array;

    /**
     * @return array
     */
    public function types(): array;

    /**
     * @param string $type
     *
     * @return bool
     */
    public function typeExists(string $type): bool;

    /**
     * @param TableFieldEntity $field
     *
     * @return int
     */
    public function typeLength(TableFieldEntity $field): int;

    /**
     * Get the driver options
     *
     * @return array
     */
    public function options(): array;

    /**
     * Get the selected database
     *
     * @return string
     */
    public function database(): string;

    /**
     * Get the selected schema
     *
     * @return string
     */
    public function schema(): string;

    /**
     * Get regular expression to match numeric types
     *
     * @return string
     */
    public function numberRegex(): string;

    /**
     * @return string
     */
    public function inout(): string;

    /**
     * Return the regular expression for queries
     *
     * @return string
     */
    public function sqlStatementRegex(): string;

    /**
     * @return string
     */
    public function enumLengthRegex(): string;

    /**
     * @return string
     */
    public function actions(): string;

    /**
     * @return array
     */
    public function onActions(): array;
}
