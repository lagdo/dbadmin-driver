<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\Entity\TableSelectEntity;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Utils\Utils;

use function implode;
use function substr;
use function str_replace;

abstract class Grammar implements GrammarInterface
{
    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var Utils
     */
    protected $utils;

    /**
     * The constructor
     *
     * @param DriverInterface $driver
     * @param Utils $utils
     */
    public function __construct(DriverInterface $driver, Utils $utils)
    {
        $this->driver = $driver;
        $this->utils = $utils;
    }

    /**
     * @inheritDoc
     */
    public function buildSelectQuery(TableSelectEntity $select)
    {
        $query = implode(', ', $select->fields) .
            ' FROM ' . $this->driver->escapeTableName($select->table);
        $limit = +$select->limit;
        $offset = $select->page ? $limit * $select->page : 0;

        return 'SELECT' . $this->driver->getLimitClause($query,
            $select->clauses, $limit, $offset);
    }

    /**
     * Return the regular expression for queries
     *
     * @return string
     */
    abstract public function queryRegex();
    // Original code from Adminer
    // {
    //     $parse = '[\'"' .
    //         ($this->driver->jush() == "sql" ? '`#' :
    //         ($this->driver->jush() == "sqlite" ? '`[' :
    //         ($this->driver->jush() == "mssql" ? '[' : ''))) . ']|/\*|-- |$' .
    //         ($this->driver->jush() == "pgsql" ? '|\$[^$]*\$' : '');
    //     return "\\s*|$parse";
    // }


    /**
     * @inheritDoc
     */
    public function escapeId(string $idf): string
    {
        return $idf;
    }

    /**
     * @inheritDoc
     */
    public function unescapeId(string $idf)
    {
        $last = substr($idf, -1);
        return str_replace($last . $last, $last, substr($idf, 1, -1));
    }

    /**
     * @inheritDoc
     */
    public function convertField(TableFieldEntity $field)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function unconvertField(TableFieldEntity $field, string $value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getCreateTableQuery(string $table, bool $autoIncrement, string $style)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getCreateIndexQuery(string $table, string $type, string $name, string $columns)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getUseDatabaseQuery(string $database, string $style = '')
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getForeignKeysQueries(TableEntity $table): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getTruncateTableQuery(string $table)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getCreateTriggerQuery(string $table)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getAutoIncrementModifier()
    {
        return '';
    }
}
