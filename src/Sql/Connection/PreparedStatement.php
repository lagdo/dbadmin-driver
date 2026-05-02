<?php

namespace Lagdo\DbAdmin\Driver\Sql\Connection;

use function array_combine;
use function array_map;
use function substr;

class PreparedStatement
{
    /**
     * @param mixed $statement
     * @param string $query
     * @param array $params
     * @param string $name
     */
    public function __construct(private mixed $statement,
        private string $query, private array $params, private string $name = '')
    {}

    /**
     * @return string
     */
    public function query(): string
    {
        return $this->query;
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
        return !$withKeys ? $paramValues : array_combine($paramNames, $paramValues);
    }
}
