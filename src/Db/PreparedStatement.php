<?php

namespace Lagdo\DbAdmin\Driver\Db;

use function array_combine;
use function array_map;
use function substr;

class PreparedStatement
{
    /**
     * The constructor
     *
     * @param string $query
     * @param mixed $statement
     * @param array $params
     * @param string $name
     */
    public function __construct(private string $query,
        private mixed $statement, private array $params, private string $name = '')
    {}

    /**
     * @return string
     */
    public function query(): string
    {
        return $this->query;
    }

    /**
     * @return bool
     */
    public function prepared(): bool
    {
        return $this->statement !== null && $this->statement !== false;
    }

    /**
     * @return mixed
     */
    public function statement(): mixed
    {
        return $this->statement;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function paramNames(): array
    {
        return array_map(fn($param) => substr($param, 1), $this->params);
    }

    /**
     * @param array $values
     * @param bool $withKeys
     *
     * @return array
     */
    public function paramValues(array $values, bool $withKeys): array
    {
        $paramNames = $this->paramNames();
        $paramValues = array_map(fn($param) => $values[$param], $paramNames);
        return !$withKeys ? $paramValues :
            array_combine($paramNames, $paramValues);
    }
}
