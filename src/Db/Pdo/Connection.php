<?php

namespace Lagdo\DbAdmin\Driver\Db\Pdo;

use Lagdo\DbAdmin\Driver\Db\Connection as AbstractConnection;
use Lagdo\DbAdmin\Driver\Db\Pdo\Statement;
use Lagdo\DbAdmin\Driver\Db\PreparedStatement;
use Lagdo\DbAdmin\Driver\Db\StatementInterface;
use Lagdo\DbAdmin\Driver\Exception\AuthException;
use Exception;
use PDO;

abstract class Connection extends AbstractConnection
{
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
            throw new AuthException($this->utils->str->html($ex->getMessage()));
        }
        $this->client->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $this->client->setAttribute(PDO::ATTR_STATEMENT_CLASS, [Statement::class]);
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
        $this->setError();
        if (!$statement) {
            [, $errno, $error] = $this->client->errorInfo();
            $this->setErrno($errno);
            $this->setError(($error) ? $error : $this->utils->trans->lang('Unknown error.'));
            return false;
        }
        // rowCount() is not guaranteed to work with all drivers
        if (($statement->numRows = $statement->rowCount()) > 0) {
            $this->setAffectedRows($statement->numRows);
        }
        return $statement;
    }

    /**
     * @inheritDoc
     */
    public function multiQuery(string $query)
    {
        $this->statement = $this->query($query);
        return $this->statement !== false;
    }

    /**
     * @inheritDoc
     */
    public function storedResult()
    {
        if (!$this->statement) {
            return null;
        }
        // rowCount() is not guaranteed to work with all drivers
        if ($this->statement->rowCount() > 0) {
            $this->setAffectedRows($this->statement->rowCount());
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
        return $this->statement->nextRowset(); // @ - PDO_PgSQL doesn't support it
    }

    /**
     * @inheritDoc
     */
    public function prepareStatement(string $query): PreparedStatement
    {
        [$params] = $this->getPreparedParams($query, false);
        $statement = $this->client->prepare($query);
        return new PreparedStatement($query, $statement, $params);
    }

    /**
     * @inheritDoc
     */
    public function executeStatement(PreparedStatement $statement,
        array $values): ?StatementInterface
    {
        if (!$statement->prepared()) {
            return null;
        }

        $values = $statement->paramValues($values, true);
        return !$statement->statement()->execute($values) ?
            null : $statement->statement();
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        $this->client = null;
    }
}
