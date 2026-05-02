<?php

namespace Lagdo\DbAdmin\Driver\Sql\Connection;

use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;

trait ConnectionTrait
{
    /**
     * @return AbstractConnection|null
     */
    abstract public function connection(): AbstractConnection|null;

    /**
     * @inheritDoc
     */
    public function extension(): string
    {
        return $this->connection()->extension();
    }

    /**
     * @inheritDoc
     */
    public function flavor(): string
    {
        return $this->connection()->flavor();
    }

    /**
     * @inheritDoc
     */
    public function serverInfo(): string
    {
        return $this->connection()->serverInfo();
    }

    /**
     * @inheritDoc
     */
    public function quote(string $string): string
    {
        return $this->connection()->quote($string);
    }

    /**
     * @inheritDoc
     */
    public function quoteBinary(string $string): string
    {
        return $this->connection()->quoteBinary($string);
    }

    /**
     * @inheritDoc
     */
    public function executeQuery(string $query, bool $unbuffered = false): QueryResultInterface
    {
        return $this->connection()->executeQuery($query, $unbuffered);
    }

    /**
     * @inheritDoc
     */
    public function affectedRows(): int
    {
        return $this->connection()->affectedRows();
    }

    /**
     * @inheritDoc
     */
    public function convertValue(mixed $value, ColumnDto $column): mixed
    {
        return $this->connection()->convertValue($value, $column);
    }

    /**
     * Create a prepared statement
     *
     * @param string $query
     *
     * @return void
     */
    public function prepareStatement(string $query): PreparedStatement
    {
        return $this->connection()->prepareStatement($query);
    }

    /**
     * Execute a prepared statement
     *
     * @param PreparedStatement $preparedStatement
     * @param array $values
     *
     * @return QueryResultInterface
     */
    public function executeStatement(PreparedStatement $preparedStatement,
        array $values): QueryResultInterface
    {
        return $this->connection()->executeStatement($preparedStatement, $values);
    }

    /**
     * @inheritDoc
     */
    public function executeMultiQuery(string $query): QueryResultInterface
    {
        return $this->connection()->executeMultiQuery($query);
    }

    /**
     * @inheritDoc
     */
    public function readRowset(QueryResultInterface $result): QueryResultInterface
    {
        return $this->connection()->readRowset($result);
    }

    /**
     * @inheritDoc
     */
    public function nextRowset(QueryResultInterface $result): bool
    {
        return $this->connection()->nextRowset($result);
    }

    /**
     * @inheritDoc
     */
    public function explain(string $query): QueryResultInterface|bool
    {
        return $this->connection()->explain($query);
    }

    /**
     * @inheritDoc
     */
    public function error(): string
    {
        return $this->connection()->error();
    }

    /**
     * @return bool
     */
    public function hasError(): bool
    {
        return $this->connection()->hasError();
    }

    /**
     * @return string
     */
    public function errorMessage(): string
    {
        return $this->connection()->errorMessage();
    }
}
