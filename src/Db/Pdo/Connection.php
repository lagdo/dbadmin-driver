<?php

namespace Lagdo\DbAdmin\Driver\Db\Pdo;

use Lagdo\DbAdmin\Driver\Db\Connection as AbstractConnection;
use Lagdo\DbAdmin\Driver\Db\StatementInterface;
use Lagdo\DbAdmin\Driver\Exception\AuthException;

use PDO;
use Exception;

abstract class Connection extends AbstractConnection
{
    /**
     * @var StatementInterface|bool
     */
    private $statement = null;

    /**
     * Create a PDO connection
     *
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param array $options
     *
     * @return void
     */
    public function dsn(string $dsn, string $username, string $password, array $options = [])
    {
        try {
            $this->client = new PDO($dsn, $username, $password, $options);
        } catch (Exception $ex) {
            // auth_error(h($ex->getMessage()));
            throw new AuthException($this->util->html($ex->getMessage()));
        }
        $this->client->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $this->client->setAttribute(PDO::ATTR_STATEMENT_CLASS, array(Statement::class));
    }

    /**
     * @inheritDoc
     */
    public function serverInfo()
    {
        return @$this->client->getAttribute(PDO::ATTR_SERVER_VERSION);
    }

    /**
     * @inheritDoc
     */
    public function quote(string $string)
    {
        return $this->client->quote($string);
    }

    /**
     * @inheritDoc
     */
    public function query(string $query, bool $unbuffered = false)
    {
        $statement = $this->client->query($query);
        $this->driver->setError();
        if (!$statement) {
            list(, $errno, $error) = $this->client->errorInfo();
            $this->driver->setErrno($errno);
            $this->driver->setError(($error) ? $error : $this->trans->lang('Unknown error.'));
            return false;
        }
        // rowCount() is not guaranteed to work with all drivers
        if (($statement->numRows = $statement->rowCount()) > 0) {
            $this->driver->setAffectedRows($statement->numRows);
        }
        return $statement;
    }

    /**
     * @inheritDoc
     */
    public function multiQuery(string $query)
    {
        return $this->statement = $this->query($query);
    }

    /**
     * @inheritDoc
     */
    public function storedResult()
    {
        if (!$this->statement) {
            return false;
        }
        // rowCount() is not guaranteed to work with all drivers
        if (($this->statement->numRows = $this->statement->rowCount()) > 0) {
            $this->driver->setAffectedRows($this->statement->numRows);
        }
        return $this->statement;
    }

    /**
     * @inheritDoc
     */
    public function nextResult()
    {
        if (!$this->statement) {
            return false;
        }
        $this->statement->offset = 0;
        return @$this->statement->nextRowset(); // @ - PDO_PgSQL doesn't support it
    }

    /**
     * @inheritDoc
     */
    public function result(string $query, int $field = -1)
    {
        if (!($statement = $this->query($query))) {
            return false;
        }
        if (!($row = $statement->fetch())) {
            return false;
        }
        return $row[$field];
    }

    /**
     * @inheritDoc
     */
    public function close()
    {
        $this->client = null;
    }
}
