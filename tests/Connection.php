<?php

namespace Lagdo\DbAdmin\Driver\Tests;

use Lagdo\DbAdmin\Driver\Db\Connection as AbstractConnection;
use Lagdo\DbAdmin\Driver\Db\StatementInterface;

class Connection extends AbstractConnection
{
    /**
     * @inheritDoc
     */
    public function serverInfo()
    {
        // TODO: Implement serverInfo() method.
    }

    /**
     * @inheritDoc
     */
    public function open(string $database, string $schema = '')
    {
        // TODO: Implement open() method.
    }

    /**
     * @inheritDoc
     */
    public function query(string $query, bool $unbuffered = false)
    {
        // TODO: Implement query() method.
    }

    /**
     * @inheritDoc
     */
    public function result(string $query, int $field = -1)
    {
        // TODO: Implement result() method.
    }

    /**
     * @inheritDoc
     */
    public function multiQuery(string $query)
    {
        // TODO: Implement multiQuery() method.
    }

    /**
     * @inheritDoc
     */
    public function storedResult()
    {
        // TODO: Implement storedResult() method.
    }

    /**
     * @inheritDoc
     */
    public function nextResult()
    {
        // TODO: Implement nextResult() method.
    }
}
