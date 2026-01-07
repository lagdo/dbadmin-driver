<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Lagdo\DbAdmin\Driver\Db\DriverConfig;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;

use function array_map;
use function array_merge;
use function array_keys;
use function array_values;
use function in_array;

trait ConfigTrait
{
    /**
     * @var DriverConfig
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
    public function structuredTypes(): array
    {
        return array_map(fn(array $types) => array_keys($types), $this->config->types);
    }

    /**
     * @return array
     */
    public function types(): array
    {
        // return call_user_func_array('array_merge', array_values($this->config->types));
        return array_merge(...array_values($this->config->types));
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function typeExists(string $type): bool
    {
        foreach ($this->config->types as $types) {
            if (isset($types[$type])) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param TableFieldEntity $field
     *
     * @return int
     */
    public function typeLength(TableFieldEntity $field): int
    {
        foreach ($this->config->types as $types) {
            if (isset($types[$field->type])) {
                return $types[$field->type] + ($field->unsigned ? 0 : 1);
            }
        }
        return 0;
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
    public function sqlStatementRegex(): string
    {
        return $this->config->sqlStatementRegex;
    }

    /**
     * @return string
     */
    public function enumLengthRegex(): string
    {
        return $this->config->enumLengthRegex;
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

    /**
     * @return array
     */
    public function fieldDefaults(): array
    {
        return ['', 'DEFAULT', ...$this->config->generated];
    }
}
