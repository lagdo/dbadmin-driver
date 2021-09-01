<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Db\ServerInterface;
use Lagdo\DbAdmin\Driver\Db\DriverInterface;
use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;

trait DbTrait
{
    /**
     * @var ServerInterface
     */
    public $server = null;

    /**
     * @var DriverInterface
     */
    public $driver = null;

    /**
     * @var ConnectionInterface
     */
    public $connection = null;

    /**
     * Connect to a given server
     *
     * @return void
     */
    public function connect()
    {
        $di = \jaxon()->di();
        $this->server = $di->get(ServerInterface::class);
        $this->connection = $di->get(ConnectionInterface::class);
        $this->driver = $di->get(DriverInterface::class);
    }
}
