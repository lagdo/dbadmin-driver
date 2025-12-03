<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Lagdo\DbAdmin\Driver\Entity\ConfigEntity;

use function in_array;

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
    public function jush(): string
    {
        return $this->config->jush;
    }

    /**
     * Get the Adminer version
     *
     * @return string
     */
    public function version(): string
    {
        return $this->config->version;
    }

    /**
     * @inheritDoc
     */
    public function support(string $feature): bool
    {
        return in_array($feature, $this->config->features);
    }

    /**
     * @return array
     */
    public function unsigned(): array
    {
        return $this->config->unsigned;
    }

    /**
     * @return array
     */
    public function functions(): array
    {
        return $this->config->functions;
    }

    /**
     * @return array
     */
    public function grouping(): array
    {
        return $this->config->grouping;
    }

    /**
     * @return array
     */
    public function operators(): array
    {
        return $this->config->operators;
    }

    /**
     * @return array
     */
    public function insertFunctions(): array
    {
        return $this->config->insertFunctions;
    }

    /**
     * @return array
     */
    public function editFunctions(): array
    {
        return $this->config->editFunctions;
    }

    /**
     * @return array
     */
    public function types(): array
    {
        return $this->config->types;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function typeExists(string $type): bool
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
    public function structuredTypes(): array
    {
        return $this->config->structuredTypes;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function setStructuredType(string $key, $value): void
    {
        $this->config->structuredTypes[$key] = $value;
    }

    /**
     * Get the driver options
     *
     * @return array
     */
    public function options(): array
    {
        return $this->config->options();
    }

    /**
     * Get the selected database
     *
     * @return string
     */
    public function database(): string
    {
        return $this->config->database;
    }

    /**
     * Get the selected schema
     *
     * @return string
     */
    public function schema(): string
    {
        return $this->config->schema;
    }

    /**
     * Get regular expression to match numeric types
     *
     * @return string
     */
    public function numberRegex(): string
    {
        return $this->config->numberRegex;
    }

    /**
     * @return string
     */
    public function inout(): string
    {
        return $this->config->inout;
    }

    /**
     * @return string
     */
    public function enumLength(): string
    {
        return $this->config->enumLength;
    }

    /**
     * @return string
     */
    public function actions(): string
    {
        return $this->config->actions;
    }

    /**
     * @return array
     */
    public function onActions(): array
    {
        return $this->config->onActions();
    }
}
