<?php

namespace Lagdo\DbAdmin\Driver;

interface ConfigInterface
{
    /**
     * Get the server jush
     *
     * @return string
     */
    public function jush();

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
}
