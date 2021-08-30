<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\DbInterface;
use Lagdo\DbAdmin\Driver\UtilInterface;

abstract class Connection implements ConnectionInterface
{
    /**
     * @var DbInterface
     */
    protected $db;

    /**
     * @var UtilInterface
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
     * Undocumented variable
     *
     * @var mixed
     */
    public $_result;

    /**
     * The constructor
     *
     * @param DbInterface $db
     * @param UtilInterface $util
     * @param ServerInterface $server
     * @param string $extension
     */
    public function __construct(DbInterface $db, UtilInterface $util, ServerInterface $server, string $extension)
    {
        $this->db = $db;
        $this->util = $util;
        $this->server = $server;
        $this->extension = $extension;
    }

    /**
     * @inheritDoc
     */
    public function quote($string)
    {
        return $string;
    }

    /**
     * @inheritDoc
     */
    public function set_charset($charset)
    {
    }

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
     * @inheritDoc
     */
    public function quoteBinary($string)
    {
        return $this->quote($string);
    }

    /**
     * @inheritDoc
     */
    public function value($val, $field)
    {
        return (is_resource($val) ? stream_get_contents($val) : $val);
    }

    /**
     * @inheritDoc
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

    /**
     * @inheritDoc
     */
    public function close()
    {
        return false;
    }
}
