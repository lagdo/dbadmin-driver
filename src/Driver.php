<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Entity\ConfigEntity;
use Lagdo\DbAdmin\Driver\Utils\Utils;
use Closure;

use function in_array;
use function Jaxon\jaxon;

abstract class Driver implements DriverInterface
{
    use Driver\ConfigTrait;
    use Driver\ConnectionTrait;
    use Driver\ServerTrait;
    use Driver\TableTrait;
    use Driver\DatabaseTrait;
    use Driver\QueryTrait;
    use Driver\GrammarTrait;

    /**
     * @var array
     */
    private static array $drivers = [];

    /**
     * The constructor
     *
     * @param Utils $utils
     * @param array $options
     */
    public function __construct(protected Utils $utils, array $options)
    {
        $this->config = new ConfigEntity($utils->trans, $options);
        $this->beforeConnection();
        // Create and set the main connection.
        $this->connection = $this->createConnection($options);
    }

    /**
     * @inheritDoc
     */
    public function support(string $feature)
    {
        return in_array($feature, $this->config->features);
    }

    /**
     * @param string $driver
     * @param Closure $closure
     *
     * @return void
     */
    public static function registerDriver(string $driver, Closure $closure): void
    {
        self::$drivers[$driver] = $closure;
    }

    /**
     * @param array $options
     *
     * @return DriverInterface|null
     */
    public static function createDriver(array $options): ?DriverInterface
    {
        $driver = $options['driver'];
        $closure = self::$drivers[$driver] ?? null;
        return !$closure ? null : $closure(jaxon()->di(), $options);
    }
}
