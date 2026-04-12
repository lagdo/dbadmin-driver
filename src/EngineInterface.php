<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Sql\Universal;
use Lagdo\DbAdmin\Driver\Sql\Specific\Config\ConfigInterface;
use Lagdo\DbAdmin\Driver\Sql\Specific;
use Lagdo\DbAdmin\Driver\Sql\Specific\Connection\ConnectionInterface;

interface EngineInterface extends ConfigInterface, ConnectionInterface,
    Universal\Engine\ServerInterface, Specific\Engine\ServerInterface,
    Universal\Engine\DatabaseInterface, Specific\Engine\DatabaseInterface,
    Universal\Engine\TableInterface, Specific\Engine\TableInterface,
    Universal\Engine\QueryInterface, Specific\Engine\QueryInterface
{
    /**
     * Get the driver name
     *
     * @return string
     */
    public function name(): string;
}
