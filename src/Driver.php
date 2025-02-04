<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;
use Lagdo\DbAdmin\Driver\Db\ServerInterface;
use Lagdo\DbAdmin\Driver\Db\DatabaseInterface;
use Lagdo\DbAdmin\Driver\Db\TableInterface;
use Lagdo\DbAdmin\Driver\Db\QueryInterface;
use Lagdo\DbAdmin\Driver\Db\GrammarInterface;
use Lagdo\DbAdmin\Driver\Utils\Utils;
use Lagdo\DbAdmin\Driver\Entity\ConfigEntity;
use Lagdo\DbAdmin\Driver\Exception\AuthException;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;

use function in_array;
use function is_object;
use function preg_match;
use function version_compare;
use function array_flip;
use function preg_quote;
use function str_replace;
use function strtr;
use function implode;
use function preg_replace;
use function preg_match_all;
use function trim;

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
     * @var Utils
     */
    protected $utils;

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
     * The constructor
     *
     * @param Utils $utils
     * @param array $options
     */
    public function __construct(Utils $utils, array $options)
    {
        $this->utils = $utils;
        $this->config = new ConfigEntity($utils->trans, $options);
        $this->beforeConnectConfig();
        // Create and set the main connection.
        $this->mainConnection = $this->createConnection();
    }

    /**
     * Set driver config
     *
     * @return void
     */
    abstract protected function beforeConnectConfig();

    /**
     * Set driver config
     *
     * @return void
     */
    abstract protected function afterConnectConfig();

    /**
     * Create a connection to the server, based on the config and available packages
     *
     * @return ConnectionInterface|null
     */
    abstract protected function createConnection();

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
    public function open(string $database, string $schema = '')
    {
        if (!$this->connection->open($database, $schema)) {
            throw new AuthException($this->error());
        }
        $this->config->database = $database;
        $this->config->schema = $schema;

        $this->afterConnectConfig();
        return $this->connection;
    }

    /**
     * @inheritDoc
     * @throws AuthException
     */
    public function connect(string $database, string $schema = '')
    {
        $this->createConnection();
        return $this->open($database, $schema);
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
        $this->utils->history->save($query);
        return $this->connection->query($query);
    }

    /**
     * @inheritDoc
     */
    public function queries()
    {
        return $this->utils->history->queries();
    }

    /**
     * @inheritDoc
     */
    public function applyQueries(string $query, array $tables, $escape = null)
    {
        if (!$escape) {
            $escape = function ($table) {
                return $this->escapeTableName($table);
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

    /**
     * Escape column key used in where()
     *
     * @param string
     *
     * @return string
     */
    public function escapeKey(string $key): string
    {
        if (preg_match('(^([\w(]+)(' . str_replace('_', '.*',
                preg_quote($this->escapeId('_'))) . ')([ \w)]+)$)', $key, $match)) {
            //! columns looking like functions
            return $match[1] . $this->escapeId($this->unescapeId($match[2])) . $match[3]; //! SQL injection
        }
        return $this->escapeId($key);
    }

    /**
     * Escape or unescape string to use inside form []
     *
     * @param string $idf
     * @param bool $back
     *
     * @return string
     */
    public function bracketEscape(string $idf, bool $back = false): string
    {
        // escape brackets inside name='x[]'
        static $trans = [':' => ':1', ']' => ':2', '[' => ':3', '"' => ':4'];
        return strtr($idf, ($back ? array_flip($trans) : $trans));
    }

    /**
     * Filter length value including enums
     *
     * @param string $length
     *
     * @return string
     */
    public function processLength(string $length): string
    {
        if (!$length) {
            return '';
        }
        $enumLength = $this->enumLength();
        if (preg_match("~^\\s*\\(?\\s*$enumLength(?:\\s*,\\s*$enumLength)*+\\s*\\)?\\s*\$~", $length) &&
            preg_match_all("~$enumLength~", $length, $matches)) {
            return '(' . implode(',', $matches[0]) . ')';
        }
        return preg_replace('~^[0-9].*~', '(\0)', preg_replace('~[^-0-9,+()[\]]~', '', $length));
    }

    /**
     * Create SQL string from field type
     *
     * @param TableFieldEntity $field
     * @param string $collate
     *
     * @return string
     */
    private function processType(TableFieldEntity $field, string $collate = 'COLLATE'): string
    {
        $collation = '';
        if (preg_match('~char|text|enum|set~', $field->type) && $field->collation) {
            $collation = " $collate " . $this->quote($field->collation);
        }
        $sign = '';
        if (preg_match($this->numberRegex(), $field->type) &&
            in_array($field->unsigned, $this->unsigned())) {
            $sign = ' ' . $field->unsigned;
        }
        return ' ' . $field->type . $this->processLength($field->length) . $sign . $collation;
    }

    /**
     * Create SQL string from field
     *
     * @param TableFieldEntity $field Basic field information
     * @param TableFieldEntity $typeField Information about field type
     *
     * @return array
     */
    public function processField(TableFieldEntity $field, TableFieldEntity $typeField): array
    {
        $onUpdate = '';
        if (preg_match('~timestamp|datetime~', $field->type) && $field->onUpdate) {
            $onUpdate = ' ON UPDATE ' . $field->onUpdate;
        }
        $comment = '';
        if ($this->support('comment') && $field->comment !== '') {
            $comment = ' COMMENT ' . $this->quote($field->comment);
        }
        $null = $field->null ? ' NULL' : ' NOT NULL'; // NULL for timestamp
        $autoIncrement = $field->autoIncrement ? $this->getAutoIncrementModifier() : null;
        return [$this->escapeId(trim($field->name)), $this->processType($typeField),
            $null, $this->getDefaultValueClause($field), $onUpdate, $comment, $autoIncrement];
    }
}
