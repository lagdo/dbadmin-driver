<?php

namespace Lagdo\DbAdmin\Driver\Driver;

interface ConfigInterface
{
    /**
     * Get the server jush
     *
     * @return string
     */
    public function jush();

    /**
     * Get the Adminer version
     *
     * @return string
     */
    public function version();

    /**
     * @return array
     */
    public function unsigned();

    /**
     * @return array
     */
    public function functions();

    /**
     * @return array
     */
    public function grouping();

    /**
     * @return array
     */
    public function operators();

    /**
     * @return array
     */
    public function editFunctions();

    /**
     * @return array
     */
    public function types();

    /**
     * @param string $type
     *
     * @return bool
     */
    public function typeExists(string $type);

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
