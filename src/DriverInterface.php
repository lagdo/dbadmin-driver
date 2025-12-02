<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Driver\ConfigInterface;
use Lagdo\DbAdmin\Driver\Driver\ConnectionInterface;
use Lagdo\DbAdmin\Driver\Driver\ServerInterface;
use Lagdo\DbAdmin\Driver\Driver\DatabaseInterface;
use Lagdo\DbAdmin\Driver\Driver\TableInterface;
use Lagdo\DbAdmin\Driver\Driver\QueryInterface;
use Lagdo\DbAdmin\Driver\Driver\GrammarInterface;

interface DriverInterface extends ConfigInterface, ConnectionInterface,
    ServerInterface, DatabaseInterface, TableInterface, QueryInterface, GrammarInterface
{
    /**
     * Get the driver name
     *
     * @return string
     */
    public function name();
}
