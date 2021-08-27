<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\AdminerDbInterface;
use Lagdo\DbAdmin\Driver\AdminerUtilInterface;

trait ConnectionTrait
{
    /**
     * @var AdminerDbInterface
     */
    protected $db;

    /**
     * @var AdminerUtilInterface
     */
    protected $util;

    /**
     * @var ServerInterface
     */
    protected $server;

    /**
     * The extension name
     *
     * @var string
     */
    public $extension;

    /**
     * The client object used to query the database server
     *
     * @var mixed
     */
    protected $client;

    /**
     * Get the client
     *
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Return a quoted string
     *
     * @param string $string
     *
     * @return string
     */
    public function quoteBinary($string)
    {
        return $this->quote($string);
    }

    /**
     * Convert value returned by database to actual value
     * @param string
     * @param array
     * @return string
     */
    public function value($val, $field)
    {
        return (is_resource($val) ? stream_get_contents($val) : $val);
    }

    /**
     * Get the default field number
     *
     * @return integer
     */
    public function defaultField()
    {
        return 0;
    }

    /**
     * Get warnings about the last command
     * @return string
     */
    public function warnings()
    {
        return '';
    }
}
