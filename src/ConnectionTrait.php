<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Db\StatementInterface;

trait ConnectionTrait
{
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
        return $this->connection->extension();
    }

    /**
     * Sets the client character set
     *
     * @param string $charset
     *
     * @return void
     */
    public function setCharset(string $charset)
    {
        $this->connection->setCharset($charset);
    }

    /**
     * Return a quoted string
     *
     * @param string $string
     *
     * @return string
     */
    public function quote(string $string)
    {
        return $this->connection->quote($string);
    }

    /**
     * Return a quoted string
     *
     * @param string $string
     *
     * @return string
     */
    public function quoteBinary(string $string)
    {
        return $this->connection->quoteBinary($string);
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
     * Get the number of rows affected by the last query
     *
     * @return integer
     */
    public function affectedRows()
    {
        return $this->connection->affectedRows();
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
     * Get the next row set of the last query
     *
     * @return bool
     */
    public function nextResult()
    {
        return $this->connection->nextResult();
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
