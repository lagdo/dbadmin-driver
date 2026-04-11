<?php

namespace Lagdo\DbAdmin\Support;

use Lagdo\DbAdmin\Support\Db\Admin;
use Lagdo\DbAdmin\Support\Db\Admin\Config\ConfigInterface;
use Lagdo\DbAdmin\Support\Db\Engine;
use Lagdo\DbAdmin\Support\Db\Engine\Connection\ConnectionInterface;

interface DriverInterface extends ConfigInterface, ConnectionInterface,
    Admin\Driver\ServerInterface, Engine\Driver\ServerInterface,
    Admin\Driver\DatabaseInterface, Engine\Driver\DatabaseInterface,
    Admin\Driver\TableInterface, Engine\Driver\TableInterface,
    Admin\Driver\QueryInterface, Engine\Driver\QueryInterface
{
    /**
     * Get the driver name
     *
     * @return string
     */
    public function name(): string;

    /**
     * Get the driver grammar
     *
     * @return GrammarInterface
     */
    public function grammar(): GrammarInterface;
}
