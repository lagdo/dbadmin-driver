<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\Driver\ConnectionInterface;
use Lagdo\DbAdmin\Driver\Utils\Utils;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Closure;

use function array_map;
use function array_key_exists;
use function count;
use function implode;
use function is_array;
use function is_resource;
use function preg_match_all;
use function stream_get_contents;
use function strlen;
use function substr;
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
    protected function options(string $name, $default = ''): mixed
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
    protected function setAffectedRows($affectedRows): void
    {
        $this->affectedRows = $affectedRows;
    }

    /**
     * @inheritDoc
     */
    public function affectedRows(): int
    {
        return $this->affectedRows;
    }

    /**
     * @inheritDoc
     */
    public function extension(): string
    {
        return $this->extension;
    }

    /**
     * @inheritDoc
     */
    public function flavor(): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function quote(string $string): string
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
    protected function setCharset(string $charset): void
    {}

    /**
     * @inheritDoc
     */
    public function quoteBinary(string $string): string
    {
        return $this->quote($string);
    }

    /**
     * @inheritDoc
     */
    public function value($value, TableFieldEntity $field): mixed
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
    public function result(string $query, int $field = -1): mixed
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
     * Replace the params in a prepared statement.
     *
     * @param string $query
     * @param Closure|null $replace
     *
     * @return array
     */
    protected function getPreparedParams(string $query, ?Closure $replace = null): array
    {
        if (!preg_match_all('/:[a-zA-Z0-9_]+/', $query, $matches,
            !$replace ? 0 : PREG_OFFSET_CAPTURE)) {
            return [[], $query];
        }
        $params = $matches[0];
        if (!$replace) {
            return [$params, $query];
        }

        // Each param is replaced separately,
        // so params with similar names are not confused.
        $queryLength = strlen($query);
        $count = count($params);
        for ($pos = 0; $pos < $count; $pos++) {
            $param = &$params[$pos];
            $npos = $pos + 1;
            $offset = $param[1] + strlen($param[0]);
            $length = ($params[$npos][1] ?? $queryLength) - $offset;
            // The replacement value is either the provided value, or the position.
            $param[] = $replace($param[0], $npos);
            // The suffix after the replacement value.
            $param[] = substr($query, $offset, $length);
        }

        return [
            // The final value of the params.
            array_map(fn(array $param) => $param[0], $params),
            // The final value of the query.
            substr($query, 0, $params[0][1]) .
                implode('', array_map(fn(array $param) =>
                    "{$param[2]}{$param[3]}", $params)),
        ];
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {}
}
