<?php

namespace Lagdo\DbAdmin\Driver\Fake;

trait DriverTrait
{
    /**
     * @var Connection
     */
    protected $testConnection;

    /**
     * @return Connection
     */
    public function connection(): Connection
    {
        return $this->testConnection;
    }

    /**
     * @param string $version
     *
     * @return void
     */
    public function setVersion(string $version)
    {
        $this->testConnection->setServerInfo($version);
    }

    /**
     * @inheritDoc
     */
    public function rows(string $query)
    {
        return $this->testConnection->rows($query);
    }
}
