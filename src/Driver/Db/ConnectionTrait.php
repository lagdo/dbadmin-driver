<?php

namespace Lagdo\DbAdmin\Driver\Driver\Db;

use Lagdo\DbAdmin\Driver\Db\Connection;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;

/**
 * Trait for Connection functions in the Driver class.
 */
trait ConnectionTrait
{
    /**
     * @var Connection
     */
    protected $connection = null;

    /**
     * @inheritDoc
     */
    public function extension()
    {
        return $this->connection->extension();
    }

    /**
     * @inheritDoc
     */
    public function serverInfo()
    {
        return $this->connection->serverInfo();
    }

    /**
     * @inheritDoc
     */
    public function quote(string $string)
    {
        return $this->connection->quote($string);
    }

    /**
     * @inheritDoc
     */
    public function quoteBinary(string $string)
    {
        return $this->connection->quoteBinary($string);
    }

    /**
     * @inheritDoc
     */
    public function affectedRows()
    {
        return $this->connection->affectedRows();
    }

    /**
     * @inheritDoc
     */
    public function result(string $query, int $field = -1)
    {
        return $this->connection->result($query, $field);
    }

    /**
     * @inheritDoc
     */
    public function multiQuery(string $query)
    {
        return $this->connection->multiQuery($query);
    }

    /**
     * @inheritDoc
     */
    public function storedResult()
    {
        return $this->connection->storedResult();
    }

    /**
     * @inheritDoc
     */
    public function nextResult()
    {
        return $this->connection->nextResult();
    }

    /**
     * @inheritDoc
     */
    public function value($value, TableFieldEntity $field)
    {
        return $this->connection->value($value, $field);
    }

    /**
     * @inheritDoc
     */
    public function explain(string $query)
    {
        return $this->connection->explain($query);
    }

    /**
     * @inheritDoc
     */
    public function error(): string
    {
        return $this->connection->error();
    }
}
