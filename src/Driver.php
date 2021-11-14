<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Entity\ConfigEntity;

use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;
use Lagdo\DbAdmin\Driver\Db\ServerInterface;
use Lagdo\DbAdmin\Driver\Db\DatabaseInterface;
use Lagdo\DbAdmin\Driver\Db\TableInterface;
use Lagdo\DbAdmin\Driver\Db\QueryInterface;
use Lagdo\DbAdmin\Driver\Db\GrammarInterface;

use Lagdo\DbAdmin\Driver\Exception\AuthException;

abstract class Driver implements DriverInterface
{
    use ConfigTrait;
    use ServerTrait;
    use TableTrait;
    use DatabaseTrait;
    use QueryTrait;
    use GrammarTrait;
    use ConnectionTrait;

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
     * @var ConfigEntity
     */
    protected $config;

    /**
     * Executed queries
     *
     * @var array
     */
    protected $queries = [];

    /**
     * Query start timestamp
     *
     * @var int
     */
    protected $start = 0;

    /**
     * The last error code
     *
     * @var int
     */
    protected $errno = 0;

    /**
     * The last error message
     *
     * @var string
     */
    protected $error = '';

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
        $this->config = new ConfigEntity($options);
        $this->initConfig();
        $this->createConnection();
    }

    /**
     * Set driver config
     *
     * @return void
     */
    abstract protected function initConfig();

    /**
     * @inheritDoc
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
    public function minVersion(string $version, string $mariaDb = '', ConnectionInterface $connection = null)
    {
        if (!$connection) {
            $connection = $this->connection;
        }
        $info = $connection->serverInfo();
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
    public function setError(string $error = '')
    {
        $this->error = $error;
    }

    /**
     * @inheritDoc
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * @inheritDoc
     */
    public function hasError()
    {
        return $this->error !== '';
    }

    /**
     * @inheritDoc
     */
    public function setErrno($errno)
    {
        $this->errno = $errno;
    }

    /**
     * @inheritDoc
     */
    public function errno()
    {
        return $this->errno;
    }

    /**
     * @inheritDoc
     */
    public function hasErrno()
    {
        return $this->errno !== 0;
    }

    /**
     * @inheritDoc
     */
    public function errorMessage()
    {
        return $this->hasErrno() ? '(' . $this->errno() . '): ' . $this->error() : $this->error();
    }

    /**
     * @inheritDoc
     */
    public function execute(string $query)
    {
        if (!$this->start) {
            $this->start = intval(microtime(true));
        }
        $this->queries[] = (preg_match('~;$~', $query) ? "DELIMITER ;;\n$query;\nDELIMITER " : $query) . ";";
        return $this->connection->query($query);
    }

    /**
     * @inheritDoc
     */
    public function queries()
    {
        return [implode("\n", $this->queries), $this->trans->formatTime($this->start)];
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
    public function values(string $query, $column = 0)
    {
        $values = [];
        $statement = $this->connection->query($query);
        if (is_object($statement)) {
            while ($row = $statement->fetchRow()) {
                $values[] = $row[$column];
            }
        }
        return $values;
    }

    /**
     * @inheritDoc
     */
    public function keyValues(string $query, ConnectionInterface $connection = null, bool $setKeys = true)
    {
        if (!is_object($connection)) {
            $connection = $this->connection;
        }
        $values = [];
        $statement = $connection->query($query);
        if (is_object($statement)) {
            while ($row = $statement->fetchRow()) {
                if ($setKeys) {
                    $values[$row[0]] = $row[1];
                } else {
                    $values[] = $row[0];
                }
            }
        }
        return $values;
    }

    /**
     * @inheritDoc
     */
    public function rows(string $query, ConnectionInterface $connection = null)
    {
        if (!$connection) {
            $connection = $this->connection;
        }
        $statement = $connection->query($query);
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
