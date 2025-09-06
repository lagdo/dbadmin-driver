<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\ErrorTrait;
use Lagdo\DbAdmin\Driver\Utils\Utils;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;

use function array_key_exists;
use function is_resource;
use function preg_match;
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

    /**
     * Return the regular expression for spaces
     *
     * @return string
     */
    protected function spaceRegex()
    {
        return "(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";
    }

    /**
     * @inheritDoc
     */
    public function execUseQuery(string $query)
    {
        $space = $this->spaceRegex();
        if (preg_match("~^$space*+USE\\b~i", $query)) {
            $this->driver->execute($query);
        }
    }
}
