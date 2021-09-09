<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Entity\ConfigEntity;

use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;
use Lagdo\DbAdmin\Driver\Db\ServerInterface;
use Lagdo\DbAdmin\Driver\Db\TableInterface;
use Lagdo\DbAdmin\Driver\Db\QueryInterface;
use Lagdo\DbAdmin\Driver\Db\GrammarInterface;

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
     * @param array $options
     */
    public function __construct(UtilInterface $util, array $options)
    {
        $this->util = $util;
        $this->util->setDriver($this);
        $this->options = $options;
        $this->config = new ConfigEntity();
        $this->setConfig();
        $this->createConnection();
    }

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
    public function numberRegex()
    {
        return '((?<!o)int(?!er)|numeric|real|float|double|decimal|money)'; // not point, not interval
    }

    /**
     * Set driver config
     *
     * @return void
     */
    abstract protected function setConfig();

    /**
     * @inheritDoc
     */
    public function connect(string $database, string $schema)
    {
        if (($database)) {
            $this->database = $database;
            $this->schema = $schema;
            $this->connection->selectDatabase($database);
            if (($schema)) {
                $this->selectSchema($schema);
            }
        }
    }

    /**
     * Set current schema
     *
     * @param string $schema
     * @param ConnectionInterface $connection
     *
     * @return bool
     */
    protected function selectSchema(string $schema, ConnectionInterface $connection = null)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function selectedDatabase()
    {
        return $this->database;
    }

    /**
     * @inheritDoc
     */
    public function selectedSchema()
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
}