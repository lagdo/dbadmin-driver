<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Lagdo\DbAdmin\Driver\Entity\ConfigEntity;

trait ConfigTrait
{
    /**
     * @var ConfigEntity
     */
    protected $config;

    /**
     * Get the server jush
     *
     * @return string
     */
    public function jush()
    {
        return $this->config->jush;
    }

    /**
     * Get the Adminer version
     *
     * @return string
     */
    public function version()
    {
        return $this->config->version;
    }

    /**
     * @return array
     */
    public function unsigned()
    {
        return $this->config->unsigned;
    }

    /**
     * @return array
     */
    public function functions()
    {
        return $this->config->functions;
    }

    /**
     * @return array
     */
    public function grouping()
    {
        return $this->config->grouping;
    }

    /**
     * @return array
     */
    public function operators()
    {
        return $this->config->operators;
    }

    /**
     * @return array
     */
    public function editFunctions()
    {
        return $this->config->editFunctions;
    }

    /**
     * @return array
     */
    public function types()
    {
        return $this->config->types;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function typeExists(string $type)
    {
        return isset($this->config->types[$type]);
    }

    /**
     * @param string $type
     *
     * @return mixed
     */
    public function type(string $type)
    {
        return $this->config->types[$type];
    }

    /**
     * @return array
     */
    public function structuredTypes()
    {
        return $this->config->structuredTypes;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function setStructuredType(string $key, $value)
    {
        $this->config->structuredTypes[$key] = $value;
    }

    /**
     * Get the driver options
     *
     * @param string $name The option name
     * @param mixed $default
     *
     * @return mixed
     */
    public function options(string $name = '', $default = '')
    {
        return $this->config->options($name, $default);
    }

    /**
     * Get the selected database
     *
     * @return string
     */
    public function database()
    {
        return $this->config->database;
    }

    /**
     * Get the selected schema
     *
     * @return string
     */
    public function schema()
    {
        return $this->config->schema;
    }

    /**
     * Get regular expression to match numeric types
     *
     * @return string
     */
    public function numberRegex()
    {
        return $this->config->numberRegex;
    }

    /**
     * @return string
     */
    public function inout()
    {
        return $this->config->inout;
    }

    /**
     * @return string
     */
    public function enumLength()
    {
        return $this->config->enumLength;
    }

    /**
     * @return string
     */
    public function actions()
    {
        return $this->config->actions;
    }

    /**
     * @return array
     */
    public function onActions()
    {
        return $this->config->onActions();
    }
}
