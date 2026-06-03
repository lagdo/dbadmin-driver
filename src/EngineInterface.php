<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Sql\Config\ConfigInterface;
use Lagdo\DbAdmin\Driver\Sql\Connection\ConnectionInterface;
use Lagdo\DbAdmin\Driver\Sql\Specific;
use Lagdo\DbAdmin\Driver\Sql\Standard;

interface EngineInterface extends ConfigInterface, ConnectionInterface,
    Standard\Engine\DatabaseInterface, Standard\Engine\QueryInterface,
    Specific\Engine\ServerInterface, Specific\Engine\DatabaseInterface,
    Specific\Engine\TableInterface, Specific\Engine\QueryInterface
{
    /**
     * Get the driver name
     *
     * @return string
     */
    public function name(): string;
}
