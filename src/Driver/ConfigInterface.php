<?php

namespace Lagdo\DbAdmin\Driver\Driver;

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
    public function types(): array;

    /**
     * @param string $type
     *
     * @return bool
     */
    public function typeExists(string $type): bool;

    /**
     * @param string $type
     *
     * @return mixed
     */
    public function type(string $type);

    /**
     * @return array
     */
    public function structuredTypes();

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function setStructuredType(string $key, $value);

    /**
     * Get the driver options
     *
     * @return array
     */
    public function options();

    /**
     * Get the selected database
     *
     * @return string
     */
    public function database();

    /**
     * Get the selected schema
     *
     * @return string
     */
    public function schema();

    /**
     * Get regular expression to match numeric types
     *
     * @return string
     */
    public function numberRegex();

    /**
     * @return string
     */
    public function inout();

    /**
     * @return string
     */
    public function enumLength();

    /**
     * @return string
     */
    public function actions();

    /**
     * @return array
     */
    public function onActions();
}
