<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Db\ErrorTrait;
use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\Utils\Utils;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;

use function array_key_exists;
use function count;
use function is_array;
use function is_resource;
use function stream_get_contents;
use function trim;

abstract class Connection implements ConnectionInterface
{
    use ErrorTrait;

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
     * @param Utils $utils
     * @param array $options
     * @param string $extension The extension name
     */
    public function __construct(protected DriverInterface $driver,
        protected Utils $utils, protected array $options, protected string $extension)
    {}

    /**
     * Connect to a database and a schema
     *
     * @param string $database  The database name
     * @param string $schema    The database schema
     *
     * @return bool
     */
    abstract public function open(string $database, string $schema = ''): bool;

    /**
     * Execute a query on the current database
     *
     * @param string $query
     * @param bool $unbuffered
     *
     * @return StatementInterface|bool
     */
    abstract public function query(string $query, bool $unbuffered = false);

    /**
     * Get warnings about the last command
     *
     * @return string
     */
    protected function warnings(): string
    {
        return '';
    }

    /**
     * Get the driver options
     *
     * @param string $name The option name
     * @param mixed $default
     *
     * @return mixed
     */
    protected function options(string $name, $default = '')
    {
        if (!($name = trim($name))) {
            return $this->options;
        }
        if (array_key_exists($name, $this->options)) {
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
        return $default;
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
     * Sets the client character set
     *
     * @param string $charset
     *
     * @return void
     */
    protected function setCharset(string $charset)
    {}

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
        return is_resource($value) ? stream_get_contents($value) : $value;
    }

    /**
     * @inheritDoc
     */
    protected function defaultField(): int
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function result(string $query, int $field = -1)
    {
        if ($field < 0) {
            $field = $this->defaultField();
        }
        $result = $this->query($query);
        if (!$result || !$result->rowCount()) {
            return null;
        }
        // return pg_fetch_result($result->result, 0, $field);
        $row = $result->fetchRow();
        return is_array($row) && count($row) > $field ? $row[$field] : null;
    }


    /**
     * @inheritDoc
     */
    public function close(): void
    {}
}
