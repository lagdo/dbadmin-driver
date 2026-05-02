<?php

namespace Lagdo\DbAdmin\Driver\Tests\Db\Fake;

use Lagdo\DbAdmin\Driver\Sql\Connection\AbstractConnection;
use Lagdo\DbAdmin\Driver\Sql\Connection\QueryResultInterface;

/**
 * Fake Connection class for testing
 */
class Connection extends AbstractConnection
{
    /**
     * @var string
     */
    protected $serverInfo = '';

    /**
     * @param string $serverInfo
     *
     * @return void
     */
    public function setServerInfo(string $serverInfo)
    {
        $this->serverInfo = $serverInfo;
    }

    /**
     * @param array $values
     *
     * @return void
     */
    public function setNextResultValues(array $values)
    {
        $this->statement = new Statement($values);
    }

    /**
     * @param bool $status
     *
     * @return void
     */
    public function setNextResultStatus(bool $status)
    {
        $this->statement = $status;
    }

    /**
     * @inheritDoc
     */
    public function serverInfo(): string
    {
        return $this->serverInfo;
    }

    /**
     * @inheritDoc
     */
    public function open(string $database, string $schema = ''): bool
    {
        return true;
        // TODO: Implement open() method.
    }

    /**
     * @inheritDoc
     */
    public function executeQuery(string $query, bool $unbuffered = false): QueryResultInterface
    {
        return $this->statement;
    }

    /**
     * @param string $query
     *
     * @return array
     */
    public function rows(string $query): array
    {
        if (!is_a($this->statement, Statement::class)) {
            return [];
        }
        return $this->statement->rows();
    }

    /**
     * @inheritDoc
     */
    public function executeMultiQuery(string $query): QueryResultInterface
    {
        // TODO: Implement multiQuery() method.
    }

    /**
     * @inheritDoc
     */
    public function readRowset(QueryResultInterface $result): QueryResultInterface
    {
        // TODO: Implement storedResult() method.
    }

    /**
     * @inheritDoc
     */
    public function nextRowset(QueryResultInterface $result): bool
    {
        // TODO: Implement nextResult() method.
    }
}
