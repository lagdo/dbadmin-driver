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
     * @param UtilInterface $util
     * @param string $server The server class name
     *
     * @return void
     */
    public function connect(UtilInterface $util, string $server)
    {
        $this->server = new $server($this, $util);
        $this->connection = $this->server->getConnection();
        $this->driver = $this->server->getDriver();
    }
}
