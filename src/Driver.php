<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;
use Lagdo\DbAdmin\Driver\Db\ServerInterface;
use Lagdo\DbAdmin\Driver\Db\DatabaseInterface;
use Lagdo\DbAdmin\Driver\Db\TableInterface;
use Lagdo\DbAdmin\Driver\Db\QueryInterface;
use Lagdo\DbAdmin\Driver\Db\GrammarInterface;
use Lagdo\DbAdmin\Driver\Entity\ConfigEntity;
use Lagdo\DbAdmin\Driver\Exception\AuthException;

use function in_array;
use function is_object;
use function preg_match;
use function version_compare;

abstract class Driver implements DriverInterface
{
    use ErrorTrait;
    use ConfigTrait;
    use ConnectionTrait;
    use ServerTrait;
    use TableTrait;
    use DatabaseTrait;
    use QueryTrait;
    use GrammarTrait;

    /**
     * @var UtilInterface
     */
    protected $util;

    /**
     * @var TranslatorInterface
     */
    protected $trans;

    /**
     * @var ServerInterface
     */
    protected $server;

    /**
     * @var DatabaseInterface
     */
    protected $database;

    /**
     * @var TableInterface
     */
    protected $table;

    /**
     * @var QueryInterface
     */
    protected $query;

    /**
     * @var GrammarInterface
     */
    protected $grammar;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var ConnectionInterface
     */
    protected $mainConnection;

    /**
     * @var ConfigEntity
     */
    protected $config;

    /**
     * @var History
     */
    protected $history;

    /**
     * The constructor
     *
     * @param UtilInterface $util
     * @param TranslatorInterface $trans
     * @param array $options
     */
    public function __construct(UtilInterface $util, TranslatorInterface $trans, array $options)
    {
        $this->util = $util;
        $this->util->setDriver($this);
        $this->trans = $trans;
        $this->config = new ConfigEntity($trans, $options);
        $this->history = new History($trans);
        $this->initDriver();
        $this->createConnection();
        // Set the current connection as the main connection.
        $this->mainConnection = $this->connection;
    }

    /**
     * @param ConnectionInterface $connection
     *
     * @return Driver
     */
    public function useConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @return Driver
     */
    public function useMainConnection()
    {
        $this->connection = $this->mainConnection;
        return $this;
    }

    /**
     * Set driver config
     *
     * @return void
     */
    abstract protected function initDriver();

    /**
     * @inheritDoc
     */
    public function support(string $feature)
    {
        return in_array($feature, $this->config->features);
    }

    /**
     * @inheritDoc
     * @throws AuthException
     */
    public function connect(string $database, string $schema)
    {
        if (!$this->connection->open($database, $schema)) {
            throw new AuthException($this->error());
        }
        $this->config->database = $database;
        $this->config->schema = $schema;
    }

    /**
     * @inheritDoc
     */
    public function minVersion(string $version, string $mariaDb = '')
    {
        $info = $this->connection->serverInfo();
        if ($mariaDb && preg_match('~([\d.]+)-MariaDB~', $info, $match)) {
            $info = $match[1];
            $version = $mariaDb;
        }
        return (version_compare($info, $version) >= 0);
    }

    /**
     * @inheritDoc
     */
    public function charset()
    {
        // SHOW CHARSET would require an extra query
        return ($this->minVersion('5.5.3', 0) ? 'utf8mb4' : 'utf8');
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
    public function setUtf8mb4(string $create)
    {
        static $set = false;
        // possible false positive
        if (!$set && preg_match('~\butf8mb4~i', $create)) {
            $set = true;
            return 'SET NAMES ' . $this->charset() . ";\n\n";
        }
        return '';
    }

    /**
     * @inheritDoc
     */
    public function execute(string $query)
    {
        $this->history->save($query);
        return $this->connection->query($query);
    }

    /**
     * @inheritDoc
     */
    public function queries()
    {
        return $this->history->queries();
    }

    /**
     * @inheritDoc
     */
    public function applyQueries(string $query, array $tables, $escape = null)
    {
        if (!$escape) {
            $escape = function ($table) {
                return $this->table($table);
            };
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
    public function keyValues(string $query, int $keyColumn = 0, int $valueColumn = 1)
    {
        $statement = $this->execute($query);
        if (!is_object($statement)) {
            return [];
        }
        $values = [];
        while ($row = $statement->fetchRow()) {
            $values[$row[$keyColumn]] = $row[$valueColumn];
        }
        return $values;
    }

    /**
     * @inheritDoc
     */
    public function rows(string $query)
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
}
