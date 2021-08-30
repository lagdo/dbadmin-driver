<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\DbInterface;
use Lagdo\DbAdmin\Driver\UtilInterface;

abstract class Connection implements ConnectionInterface
{
    use ConnectionTrait;

    /**
     * Undocumented variable
     *
     * @var mixed
     */
    public $_result;

    /**
     * The server description
     *
     * @var string
     */
    public $server_info;

    /**
     * Undocumented variable
     *
     * @var int
     */
    public $affected_rows;

    /**
     * Undocumented variable
     *
     * @var int
     */
    public $errno;

    /**
     * Undocumented variable
     *
     * @var string
     */
    public $error;

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
     * Return a quoted string
     *
     * @param string $string
     *
     * @return string
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
     * @inheritDoc
     */
    public function close()
    {
        return false;
    }
}
