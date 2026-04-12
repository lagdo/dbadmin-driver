<?php

namespace Lagdo\DbAdmin\Support;

use Lagdo\DbAdmin\Support\Db\Admin;
use Lagdo\DbAdmin\Support\Db\Admin\Config\DriverConfig;
use Lagdo\DbAdmin\Support\Db\Engine;
use Lagdo\DbAdmin\Support\Utils\Utils;
use Closure;

abstract class AbstractDriver implements DriverInterface
{
    use Admin\Config\ConfigTrait;
    use Engine\Connection\ConnectionTrait;
    use Admin\Driver\ServerTrait;
    use Engine\Driver\ServerTrait;
    use Admin\Driver\DatabaseTrait;
    use Engine\Driver\DatabaseTrait;
    use Admin\Driver\TableTrait;
    use Engine\Driver\TableTrait;
    use Admin\Driver\QueryTrait;
    use Engine\Driver\QueryTrait;

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
        $this->config = new DriverConfig($utils->trans, $options);
        $this->_server()->initConnection($this->config, $options);
    }

    /**
     * @return AbstractDriver
     */
    protected function _driver(): AbstractDriver
    {
        return $this;
    }

    /**
     * @return GrammarInterface
     */
    public function grammar(): GrammarInterface
    {
        return $this->_grammar();
    }

    /**
     * @return Utils
     */
    protected function _utils(): Utils
    {
        return $this->utils;
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
     * @return array<Closure>
     */
    public static function drivers(): array
    {
        return self::$drivers;
    }
}
