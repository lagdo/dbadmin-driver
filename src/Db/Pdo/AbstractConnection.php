<?php

namespace Lagdo\DbAdmin\Driver\Db\Pdo;

use Lagdo\DbAdmin\Driver\Db\AbstractConnection as BaseConnection;
use Lagdo\DbAdmin\Driver\Db\PreparedStatement;
use Lagdo\DbAdmin\Driver\Db\StatementInterface;
use Lagdo\Facades\Logger;
use Exception;
use PDO;

abstract class AbstractConnection extends BaseConnection
{
    /**
     * Create a PDO connection
     *
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param array $options
     *
     * @return bool
     */
    public function dsn(string $dsn, string $username, string $password, array $options = []): bool
    {
        try {
            $this->client = new PDO($dsn, $username, $password, $options);
            $this->client->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            $this->client->setAttribute(PDO::ATTR_STATEMENT_CLASS, [Statement::class]);
            $this->client->setAttribute(PDO::ATTR_TIMEOUT, 2);
        } catch (Exception $ex) {
            $this->client = null;
            Logger::error("Unable to connect to database using PDO", [
                'dsn' => $dsn,
                'username' => $username,
                'error' => $ex->getMessage(),
            ]);
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function serverInfo(): string
    {
        return @$this->client?->getAttribute(PDO::ATTR_SERVER_VERSION) ?? '';
    }

    /**
     * @inheritDoc
     */
    public function quote(string $string): string
    {
        return $this->client->quote($string);
    }

    /**
     * @inheritDoc
     */
    public function query(string $query, bool $unbuffered = false): StatementInterface|bool
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
    public function multiQuery(string $query): bool
    {
        $this->statement = $this->query($query);
        return $this->statement !== false;
    }

    /**
     * @inheritDoc
     */
    public function storedResult(): StatementInterface|bool
    {
        if (!$this->statement) {
            return false;
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
    public function nextResult(): mixed
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
        [$params] = $this->getPreparedParams($query);
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
        return !$statement->statement()->execute($values) ? null : $statement->statement();
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        $this->client = null;
    }
}
