<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Sql\Config\ConfigInterface;
use Lagdo\DbAdmin\Driver\Sql\Connection\ConnectionInterface;
use Lagdo\DbAdmin\Driver\Sql\Specific;
use Lagdo\DbAdmin\Driver\Sql\Standard;

interface EngineInterface extends ConfigInterface, ConnectionInterface,
    Standard\Engine\ServerInterface, Specific\Engine\ServerInterface,
    Standard\Engine\DatabaseInterface, Specific\Engine\DatabaseInterface,
    Standard\Engine\TableInterface, Specific\Engine\TableInterface,
    Standard\Engine\QueryInterface, Specific\Engine\QueryInterface
{
    /**
     * Get the driver name
     *
     * @return string
     */
    public function name(): string;
}
