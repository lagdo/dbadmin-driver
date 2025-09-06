<?php

namespace Lagdo\DbAdmin\Driver\Fake;

use Lagdo\DbAdmin\Driver\Db\Connection as AbstractConnection;

use function count;

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
    public function serverInfo()
    {
        return $this->serverInfo;
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
        return $this->statement;
    }

    /**
     * @inheritDoc
     */
    public function result(string $query, int $field = -1)
    {
        if ($field < 0) {
            $field = $this->defaultField();
        }
        if (!is_a($this->statement, Statement::class)) {
            return null;
        }
        $row = $this->statement->fetchRow();
        return is_array($row) && count($row) > $field ? $row[$field] : null;
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
