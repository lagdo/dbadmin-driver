<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Entity\ConfigEntity;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;

use Lagdo\DbAdmin\Driver\Db\StatementInterface;

trait ConnectionTrait
{
    /**
     * Get information about the last query
     *
     * @return string
     */
    public function info()
    {
        return $this->connection->info;
    }

    /**
     * Get the server description
     *
     * @return string
     */
    public function serverInfo()
    {
        return $this->connection->serverInfo();
    }

    /**
     * Get the driver extension
     *
     * @return string
     */
    public function extension()
    {
        return $this->connection->extension;
    }

    /**
     * Sets the client character set
     *
     * @param string $charset
     *
     * @return bool
     */
    public function setCharset(string $charset)
    {
        return $this->connection->setCharset($charset);
    }

    /**
     * Execute a query on the current database
     *
     * @param string $query
     * @param bool $unbuffered
     *
     * @return StatementInterface|bool
     */
    public function query(string $query, bool $unbuffered = false)
    {
        return $this->connection->query($query, $unbuffered);
    }

    /**
     * Execute a query on the current database and fetch the specified field
     *
     * @param string $query
     * @param int $field
     *
     * @return mixed
     */
    public function result(string $query, int $field = -1)
    {
        return $this->connection->result($query, $field);
    }

    /**
     * Get the next row set of the last query
     *
     * @return bool
     */
    public function nextResult()
    {
        return $this->connection->nextResult();
    }

    /**
     * Execute a query on the current database and store the result
     *
     * @param string $query
     *
     * @return bool
     */
    public function multiQuery(string $query)
    {
        return $this->connection->multiQuery($query);
    }

    /**
     * Get the result saved by the multiQuery() method
     *
     * @return StatementInterface|bool
     */
    public function storedResult()
    {
        return $this->connection->storedResult();
    }

    /**
     * Convert value returned by database to actual value
     *
     * @param string|resource|null $value
     * @param TableFieldEntity $field
     *
     * @return string
     */
    public function value($value, TableFieldEntity $field)
    {
        return $this->connection->value($value, $field);
    }
}
