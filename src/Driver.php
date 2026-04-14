<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Utils\Utils;
use Closure;

class Driver
{
    /**
     * Closures to create driver engines and statements.
     *
     * @var array
     */
    private static array $builders = [];

    /**
     * @param AbstractEngine $engine
     * @param AbstractStatement $statement
     */
    public function __construct(public readonly AbstractEngine $engine,
        public readonly AbstractStatement $statement)
    {}

    /**
     * @param string $driver
     * @param Closure $builder
     *
     * @return void
     */
    public static function registerBuilder(string $driver, Closure $builder): void
    {
        self::$builders[$driver] = $builder;
    }

    /**
     * @param Utils $utils
     * @param array $options
     *
     * @return Driver|null
     */
    public static function createDriver(Utils $utils, array $options): Driver|null
    {
        $builder = self::$builders[$options['driver']] ?? null;
        [$engine, $statement] = $builder === null ? [null, null] : $builder($utils, $options);
        if ($engine === null || $statement === null) {
            return null;
        }

        $statement->setEngine($engine);
        $engine->setStatement($statement);
        return new self($engine, $statement);
    }
}
