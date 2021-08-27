<?php

namespace Lagdo\Adminer\Driver;

use Lagdo\Adminer\Driver\Db\ServerInterface;
use Lagdo\Adminer\Driver\Db\DriverInterface;
use Lagdo\Adminer\Driver\Db\ConnectionInterface;

trait AdminerDbTrait
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
     * @param AdminerUtilInterface $util
     * @param string $server The server class name
     *
     * @return void
     */
    public function connect(AdminerUtilInterface $util, string $server)
    {
        $this->server = new $server($this, $util);
        $this->connection = $this->server->getConnection();
        $this->driver = $this->server->getDriver();
    }
}
