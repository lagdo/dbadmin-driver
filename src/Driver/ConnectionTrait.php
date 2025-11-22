<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;
use Lagdo\DbAdmin\Driver\Db\PreparedStatement;
use Lagdo\DbAdmin\Driver\Db\StatementInterface;
use Lagdo\DbAdmin\Driver\Exception\AuthException;

use function is_object;
use function preg_match;
use function version_compare;

trait ConnectionTrait
{
    use Db\ConnectionTrait;

    /**
     * @var ConnectionInterface
     */
    protected $mainConnection = null;

    /**
     * @return void
     */
    protected function beforeConnection()
    {}

    /**
     * @return void
     */
    protected function configConnection()
    {}

    /**
     * @return void
     */
    protected function openedConnection()
    {}

    /**
     * @inheritDoc
     */
    public function connection(): ConnectionInterface|null
    {
        return $this->connection;
    }

    /**
     * @inheritDoc
     * @throws AuthException
     */
    public function open(string $database, string $schema = ''): ConnectionInterface
    {
        if (!$this->connection->open($database, $schema)) {
            throw new AuthException($this->error());
        }

        $this->config->database = $database;
        $this->config->schema = $schema;
        if ($this->mainConnection === null) {
            $this->mainConnection = $this->connection;
            $this->configConnection();
        }
        $this->openedConnection();
        return $this->connection;
    }

    /**
     * @inheritDoc
     */
    public function close(): void
    {
        $this->connection->close();
        $this->connection = null;
    }

    /**
     * @inheritDoc
     */
    public function connectToDatabase(string $database, string $schema = ''): ConnectionInterface|null
    {
        $connection = $this->createConnection($this->config->options());
        return !$connection || !$connection->open($database, $schema) ? null : $connection;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $query)
    {
        return $this->connection->query($query);
    }

    /**
     * @inheritDoc
     */
    public function applyQueries(string $query, array $tables, $escape = null)
    {
        if (!$escape) {
            $escape = fn ($table) => $this->escapeTableName($table);
        }
        foreach ($tables as $table) {
            if (!$this->execute("$query " . $escape($table))) {
                return false;
            }
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function values(string $query, int $column = 0)
    {
        $statement = $this->execute($query);
        if (!is_object($statement)) {
            return [];
        }
        $values = [];
        while ($row = $statement->fetchRow()) {
            $values[] = $row[$column];
        }
        return $values;
    }

    /**
     * @inheritDoc
     */
    public function colValues(string $query, string $column)
    {
        $statement = $this->execute($query);
        if (!is_object($statement)) {
            return [];
        }
        $values = [];
        while ($row = $statement->fetchAssoc()) {
            $values[] = $row[$column];
        }
        return $values;
    }

    /**
     * @inheritDoc
     */
    public function rows(string $query): array
    {
        $statement = $this->execute($query);
        if (!is_object($statement)) { // can return true
            return [];
        }
        $rows = [];
        while ($row = $statement->fetchAssoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * @inheritDoc
     */
    public function keyValues(string $query, bool $setKeys = true)
    {
        $statement = $this->execute($query);
        if (!is_object($statement)) {
            return [];
        }
        $values = [];
        while ($row = $statement->fetchRow()) {
            if ($setKeys) {
                $values[$row[0]] = $row[1];
            } else {
                $values[] = $row[0];
            }
        }
        return $values;
    }

    /**
     * @inheritDoc
     */
    public function begin()
    {
        $result = $this->connection->query("BEGIN");
        return $result !== false;
    }

    /**
     * @inheritDoc
     */
    public function commit()
    {
        $result = $this->connection->query("COMMIT");
        return $result !== false;
    }

    /**
     * @inheritDoc
     */
    public function rollback()
    {
        $result = $this->connection->query("ROLLBACK");
        return $result !== false;
    }

    /**
     * @inheritDoc
     */
    public function minVersion(string $version, string $mariaDb = ''): bool
    {
        $info = $this->connection->serverInfo();
        if ($mariaDb && preg_match('~([\d.]+)-MariaDB~', $info, $match)) {
            $info = $match[1];
            $version = $mariaDb;
        }
        return $version && version_compare($info, $version) >= 0;
    }

    /**
     * @inheritDoc
     */
    public function charset(): string
    {
        // SHOW CHARSET would require an extra query
        return $this->minVersion('5.5.3') ? 'utf8mb4' : 'utf8';
    }

    /**
     * Create a prepared statement
     *
     * @param string $query
     *
     * @return void
     */
    public function prepareStatement(string $query): PreparedStatement
    {
        return $this->connection->prepareStatement($query);
    }

    /**
     * Execute a prepared statement
     *
     * @param PreparedStatement $statement
     * @param array $values
     *
     * @return StatementInterface|bool
     */
    public function executeStatement(PreparedStatement $statement,
        array $values): ?StatementInterface
    {
        return $this->connection->executeStatement($statement, $values);
    }
}
