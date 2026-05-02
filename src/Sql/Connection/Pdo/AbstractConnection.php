<?php

namespace Lagdo\DbAdmin\Driver\Sql\Connection\Pdo;

use Lagdo\DbAdmin\Driver\Sql\Connection\PreparedStatement;
use Lagdo\DbAdmin\Driver\Sql\Connection\QueryResultInterface;
use Lagdo\DbAdmin\Driver\Sql\Connection\AbstractConnection as BaseConnection;
use Lagdo\Facades\Logger;
use Exception;
use PDO;
use PDOStatement;

abstract class AbstractConnection extends BaseConnection
{
    /**
     * The client object used to query the database driver
     *
     * @var PDO|null
     */
    protected PDO|null $client;

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
            // $this->client->setAttribute(PDO::ATTR_STATEMENT_CLASS, [Statement::class]);
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
    public function executeQuery(string $query, bool $unbuffered = false): QueryResultInterface
    {
        $statement = $this->client->query($query);
        $this->setError();
        if (!$statement) {
            [, $errno, $error] = $this->client->errorInfo();
            $this->setErrno($errno);
            $this->setError(($error) ? $error : $this->_utils()->lang('Unknown error.'));
            return new QueryResult(false);
        }

        // rowCount() is not guaranteed to work with all drivers
        if (($numRows = $statement->rowCount()) > 0) {
            $this->setAffectedRows($numRows);
        }
        return new QueryResult($statement);
    }

    /**
     * @inheritDoc
     */
    public function prepareStatement(string $query): PreparedStatement
    {
        [$params] = $this->getPreparedParams($query);
        $statement = $this->client->prepare($query);
        return new PreparedStatement($statement, $query, $params);
    }

    /**
     * @inheritDoc
     */
    public function executeStatement(PreparedStatement $preparedStatement,
        array $values): QueryResultInterface
    {
        /** @var PDOStatement|bool */
        $statement = $preparedStatement->statement();
        if (!$statement) {
            $this->setError($this->_utils()->lang($this->statementNotPrepared));
            return new QueryResult(false);
        }

        $values = $preparedStatement->paramValues($values, true);
        return new QueryResult(!$statement->execute($values) ? false : $statement);
    }

    /**
     * @inheritDoc
     */
    public function executeMultiQuery(string $query): QueryResultInterface
    {
        return $this->executeQuery($query);
    }

    /**
     * @inheritDoc
     */
    public function readRowset(QueryResultInterface $result): QueryResultInterface
    {
        // rowCount() is not guaranteed to work with all drivers
        if ($result->rowCount() > 0) {
            $this->setAffectedRows($result->rowCount());
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function nextRowset(QueryResultInterface $result): bool
    {
        /** @var QueryResult */
        $pdoResult = $result;
        // @ - PDO_PgSQL doesn't support it
        return $pdoResult->statement()?->nextRowset() ?? false;
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        $this->client = null;
    }
}
