<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Entity\ConfigEntity;

use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;
use Lagdo\DbAdmin\Driver\Db\ServerInterface;
use Lagdo\DbAdmin\Driver\Db\TableInterface;
use Lagdo\DbAdmin\Driver\Db\QueryInterface;
use Lagdo\DbAdmin\Driver\Db\GrammarInterface;

use Lagdo\DbAdmin\Driver\Exception\AuthException;

abstract class Driver implements DriverInterface, ServerInterface, TableInterface, QueryInterface, GrammarInterface
{
    use ConfigTrait;
    use ServerTrait;
    use TableTrait;
    use QueryTrait;
    use GrammarTrait;
    use ConnectionTrait;

    /**
     * @var UtilInterface
     */
    protected $util;

    /**
     * @var TranslatorInterface
     */
    protected $trans;

    /**
     * @var ServerInterface
     */
    protected $server;

    /**
     * @var TableInterface
     */
    protected $table;

    /**
     * @var QueryInterface
     */
    protected $query;

    /**
     * @var GrammarInterface
     */
    protected $grammar;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var ConfigEntity
     */
    protected $config = null;

    /**
     * From bootstrap.inc.php
     * @var string
     */
    public $onActions = "RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT"; ///< @var string used in foreignKeys()

    /**
     * From index.php
     * @var string
     */
    public $enumLength = "'(?:''|[^'\\\\]|\\\\.)*'";

    /**
     * From index.php
     * @var string
     */
    public $inout = "IN|OUT|INOUT";

    /**
     * @var string
     */
    protected $database = '';

    /**
     * @var string
     */
    protected $schema = '';

    /**
     * The constructor
     *
     * @param UtilInterface $util
     * @param TranslatorInterface $trans
     * @param array $options
     */
    public function __construct(UtilInterface $util, TranslatorInterface $trans, array $options)
    {
        $this->util = $util;
        $this->util->setDriver($this);
        $this->trans = $trans;
        $this->options = $options;
        $this->config = new ConfigEntity();
        $this->initConfig();
        $this->createConnection();
    }

    /**
     * Set driver config
     *
     * @return void
     */
    abstract protected function initConfig();

    /**
     * @inheritDoc
     */
    public function version()
    {
        return "4.8.1-dev";
    }

    /**
     * @inheritDoc
     */
    public function options(string $name = '')
    {
        if (!($name = trim($name))) {
            return $this->options;
        }
        if (\array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }
        if ($name === 'server') {
            $server = $this->options['host'] ?? '';
            $port = $this->options['port'] ?? ''; // Optional
            // Append the port to the host if it is defined.
            if (($port)) {
                $server .= ":$port";
            }
            return $server;
        }
        // if ($name === 'ssl') {
        //     return false; // No SSL options yet
        // }
        // Option not found
        return '';
    }

    /**
     * @inheritDoc
     */
    public function connect(string $database, string $schema)
    {
        $this->database = $database;
        $this->schema = $schema;
        if (!$this->connection->open($database, $schema)) {
            throw new AuthException($this->error());
        }
    }

    /**
     * @inheritDoc
     */
    public function numberRegex()
    {
        return '((?<!o)int(?!er)|numeric|real|float|double|decimal|money)'; // not point, not interval
    }

    /**
     * @inheritDoc
     */
    public function database()
    {
        return $this->database;
    }

    /**
     * @inheritDoc
     */
    public function schema()
    {
        return $this->schema;
    }

    /**
     * @inheritDoc
     */
    public function config()
    {
        return $this->config;
    }

    /**
     * @inheritDoc
     */
    public function minVersion(string $version, string $mariaDb = "", ConnectionInterface $connection = null)
    {
        if (!$connection) {
            $connection = $this->connection;
        }
        $info = $connection->serverInfo();
        if ($mariaDb && preg_match('~([\d.]+)-MariaDB~', $info, $match)) {
            $info = $match[1];
            $version = $mariaDb;
        }
        return (version_compare($info, $version) >= 0);
    }

    /**
     * @inheritDoc
     */
    public function charset()
    {
        // SHOW CHARSET would require an extra query
        return ($this->minVersion("5.5.3", 0) ? "utf8mb4" : "utf8");
    }

    /**
     * @inheritDoc
     */
    public function setUtf8mb4(string $create)
    {
        static $set = false;
        // possible false positive
        if (!$set && preg_match('~\butf8mb4~i', $create)) {
            $set = true;
            return "SET NAMES " . $this->charset() . ";\n\n";
        }
        return '';
    }
}
