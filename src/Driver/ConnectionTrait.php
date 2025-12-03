<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;
use Lagdo\DbAdmin\Driver\Db\PreparedStatement;
use Lagdo\DbAdmin\Driver\Db\StatementInterface;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;

trait ConnectionTrait
{
    /**
     * @return ConnectionInterface|null
     */
    abstract public function connection(): ConnectionInterface|null;

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
    public function query(string $query, bool $unbuffered = false): StatementInterface|bool
    {
        return $this->connection()->query($query, $unbuffered);
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
    public function result(string $query, int $field = -1): mixed
    {
        return $this->connection()->result($query, $field);
    }

    /**
     * @inheritDoc
     */
    public function multiQuery(string $query): bool
    {
        return $this->connection()->multiQuery($query);
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
     * @param PreparedStatement $statement
     * @param array $values
     *
     * @return StatementInterface|bool
     */
    public function executeStatement(PreparedStatement $statement,
        array $values): ?StatementInterface
    {
        return $this->connection()->executeStatement($statement, $values);
    }

    /**
     * @inheritDoc
     */
    public function storedResult(): StatementInterface|bool
    {
        return $this->connection()->storedResult();
    }

    /**
     * @inheritDoc
     */
    public function nextResult(): mixed
    {
        return $this->connection()->nextResult();
    }

    /**
     * @inheritDoc
     */
    public function value($value, TableFieldEntity $field): mixed
    {
        return $this->connection()->value($value, $field);
    }

    /**
     * @inheritDoc
     */
    public function explain(string $query): StatementInterface|bool
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
