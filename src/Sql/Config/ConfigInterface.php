<?php

namespace Lagdo\DbAdmin\Driver\Sql\Config;

use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;

interface ConfigInterface
{
    /**
     * Get the database engine name
     *
     * @return string
     */
    public function jush(): string;

    /**
     * Check if the driver is MySQL or MariaDB.
     *
     * @return bool
     */
    public function sql(): bool;

    /**
     * Check if the driver is MySQL.
     *
     * @return bool
     */
    public function mysql(): bool;

    /**
     * Check if the driver is MariaDB.
     *
     * @return bool
     */
    public function maria(): bool;

    /**
     * Check if the driver is PostgreSQL.
     *
     * @return bool
     */
    public function pgsql(): bool;

    /**
     * Check if the driver is SQLite.
     *
     * @return bool
     */
    public function sqlite(): bool;

    /**
     * Check if the driver is Microsoft SQL Server.
     *
     * @return bool
     */
    public function mssql(): bool;

    /**
     * Check if the driver is Oracle.
     *
     * @return bool
     */
    public function oracle(): bool;

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
     * @param ColumnDto $column
     *
     * @return int
     */
    public function typeLength(ColumnDto $column): int;

    /**
     * @param string $type
     *
     * @return bool
     */
    public function typeIsAutoIncrementable(string $type): bool;

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

    /**
     * @return array
     */
    public function columnDefaults(): array;
}
