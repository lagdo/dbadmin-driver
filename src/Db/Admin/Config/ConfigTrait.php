<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Config;

use Lagdo\DbAdmin\Support\Dto\TableFieldDto;

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
    private DriverConfig $config;

    /**
     * @return DriverConfig
     */
    protected function config(): DriverConfig
    {
        return $this->config;
    }

    /**
     * Get the database engine name
     *
     * @return string
     */
    public function jush(): string
    {
        return $this->config()->jush;
    }

    /**
     * Get the database flavor
     *
     * @return string
     */
    abstract public function flavor(): string;

    /**
     * Check if the driver is MySQL or MariaDB.
     *
     * @return bool
     */
    public function sql(): bool
    {
        return $this->jush() === 'sql';
    }

    /**
     * @return bool
     */
    public function mysql(): bool
    {
        return $this->jush() === 'sql' && $this->flavor() === 'mysql';
    }

    /**
     * @return bool
     */
    public function maria(): bool
    {
        return $this->jush() === 'sql' && $this->flavor() === 'maria';
    }

    /**
     * @return bool
     */
    public function pgsql(): bool
    {
        return $this->jush() === 'pgsql';
    }

    /**
     * @return bool
     */
    public function sqlite(): bool
    {
        return $this->jush() === 'sqlite';
    }

    /**
     * @return bool
     */
    public function mssql(): bool
    {
        return $this->jush() === 'mssql';
    }

    /**
     * @return bool
     */
    public function oracle(): bool
    {
        return $this->jush() === 'oracle';
    }

    /**
     * Get the Adminer version
     *
     * @return string
     */
    public function version(): string
    {
        return $this->config()->version;
    }

    /**
     * @inheritDoc
     */
    public function support(string $feature): bool
    {
        return in_array($feature, $this->config()->features);
    }

    /**
     * @return array
     */
    public function unsigned(): array
    {
        return $this->config()->unsigned;
    }

    /**
     * @return array
     */
    public function functions(): array
    {
        return $this->config()->functions;
    }

    /**
     * @return array
     */
    public function grouping(): array
    {
        return $this->config()->grouping;
    }

    /**
     * @return array
     */
    public function operators(): array
    {
        return $this->config()->operators;
    }

    /**
     * @return array
     */
    public function insertFunctions(): array
    {
        return $this->config()->insertFunctions;
    }

    /**
     * @return array
     */
    public function editFunctions(): array
    {
        return $this->config()->editFunctions;
    }

    /**
     * @return array
     */
    public function structuredTypes(): array
    {
        return $this->sqlite() ? array_keys($this->config()->types[0]) :
            array_map(array_keys(...), $this->config()->types);
    }

    /**
     * @return array
     */
    public function types(): array
    {
        // return call_user_func_array('array_merge', array_values($this->config()->types));
        return array_merge(...array_values($this->config()->types));
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function typeExists(string $type): bool
    {
        foreach ($this->config()->types as $types) {
            if (isset($types[$type])) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param TableFieldDto $field
     *
     * @return int
     */
    public function typeLength(TableFieldDto $field): int
    {
        foreach ($this->config()->types as $types) {
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
        return $this->config()->options();
    }

    /**
     * Get the selected database
     *
     * @return string
     */
    public function database(): string
    {
        return $this->config()->database;
    }

    /**
     * Get the selected schema
     *
     * @return string
     */
    public function schema(): string
    {
        return $this->config()->schema;
    }

    /**
     * Get regular expression to match numeric types
     *
     * @return string
     */
    public function numberRegex(): string
    {
        return $this->config()->numberRegex;
    }

    /**
     * @return string
     */
    public function inout(): string
    {
        return $this->config()->inout;
    }

    /**
     * @return string
     */
    public function sqlStatementRegex(): string
    {
        return $this->config()->sqlStatementRegex;
    }

    /**
     * @return string
     */
    public function enumLengthRegex(): string
    {
        return $this->config()->enumLengthRegex;
    }

    /**
     * @return string
     */
    public function actions(): string
    {
        return $this->config()->actions;
    }

    /**
     * @return array
     */
    public function onActions(): array
    {
        return $this->config()->onActions();
    }

    /**
     * @return array
     */
    public function fieldDefaults(): array
    {
        return ['', 'DEFAULT', ...$this->config()->generated];
    }
}
