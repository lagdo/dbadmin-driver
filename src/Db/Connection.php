<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\UtilInterface;
use Lagdo\DbAdmin\Driver\TranslatorInterface;

use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;

use function is_resource;
use function stream_get_contents;

abstract class Connection implements ConnectionInterface
{
    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var UtilInterface
     */
    protected $util;

    /**
     * @var TranslatorInterface
     */
    protected $trans;

    /**
     * The extension name
     *
     * @var string
     */
    protected $extension;

    /**
     * The client object used to query the database driver
     *
     * @var mixed
     */
    protected $client;

    /**
     * @var mixed
     */
    public $statement;

    /**
     * The number of rows affected by the last query
     *
     * @var int
     */
    protected $affectedRows;

    /**
     * The constructor
     *
     * @param DriverInterface $driver
     * @param UtilInterface $util
     * @param TranslatorInterface $trans
     * @param string $extension
     */
    public function __construct(DriverInterface $driver, UtilInterface $util, TranslatorInterface $trans, string $extension)
    {
        $this->driver = $driver;
        $this->util = $util;
        $this->trans = $trans;
        $this->extension = $extension;
    }

    /**
     * Set the number of rows affected by the last query
     *
     * @param int $affectedRows
     *
     * @return void
     */
    protected function setAffectedRows($affectedRows)
    {
        $this->affectedRows = $affectedRows;
    }

    /**
     * @inheritDoc
     */
    public function affectedRows()
    {
        return $this->affectedRows;
    }

    /**
     * @inheritDoc
     */
    public function extension()
    {
        return $this->extension;
    }

    /**
     * @inheritDoc
     */
    public function quote(string $string)
    {
        return $string;
    }

    /**
     * @inheritDoc
     */
    public function setCharset(string $charset)
    {
    }

    /**
     * Get the client
     *
     * @return mixed
     */
    public function client()
    {
        return $this->client;
    }

    /**
     * @inheritDoc
     */
    public function quoteBinary(string $string)
    {
        return $this->quote($string);
    }

    /**
     * @inheritDoc
     */
    public function value($value, TableFieldEntity $field)
    {
        return (is_resource($value) ? stream_get_contents($value) : $value);
    }

    /**
     * @inheritDoc
     */
    public function defaultField()
    {
        return 0;
    }

    /**
     * @inheritDoc
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
        return;
    }
}
