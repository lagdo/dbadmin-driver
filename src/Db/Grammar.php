<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Entity\ForeignKeyEntity;

use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\UtilInterface;
use Lagdo\DbAdmin\Driver\TranslatorInterface;
use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;

abstract class Grammar implements GrammarInterface
{
    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var UtilInterface
     */
    protected $util;

    /**
     * @var TranslatorInterface
     */
    protected $trans;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * The constructor
     *
     * @param DriverInterface $driver
     * @param UtilInterface $util
     * @param TranslatorInterface $trans
     * @param ConnectionInterface $connection
     */
    public function __construct(DriverInterface $driver, UtilInterface $util, TranslatorInterface $trans, ConnectionInterface $connection)
    {
        $this->driver = $driver;
        $this->util = $util;
        $this->trans = $trans;
        $this->connection = $connection;
    }

    /**
     * @inheritDoc
     */
    public function escapeId(string $idf)
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
    public function table(string $idf)
    {
        return $this->escapeId($idf);
    }

    /**
     * @inheritDoc
     */
    public function formatForeignKey(ForeignKeyEntity $foreignKey)
    {
        $db = $foreignKey->db;
        $schema = $foreignKey->schema;
        return " FOREIGN KEY (" . implode(", ", array_map(function ($idf) {
            return $this->escapeId($idf);
        }, $foreignKey->source)) . ") REFERENCES " .
            ($db != "" && $db != $this->driver->database ? $this->escapeId($db) . "." : "") .
            ($schema != "" && $schema != $this->driver->schema ? $this->escapeId($schema) . "." : "") .
            $this->table($foreignKey->table) . " (" . implode(", ", array_map(function ($idf) {
                return $this->escapeId($idf);
            }, $foreignKey->target)) . ")" . //! reuse $name - check in older MySQL versions
            (preg_match("~^({$this->driver->onActions})\$~", $foreignKey->onDelete) ? " ON DELETE $foreignKey->onDelete" : "") .
            (preg_match("~^({$this->driver->onActions})\$~", $foreignKey->onUpdate) ? " ON UPDATE $foreignKey->onUpdate" : "")
        ;
    }

    /**
     * @inheritDoc
     */
    public function quote($string)
    {
        return $this->connection->quote($string);
    }

    /**
     * @inheritDoc
     */
    public function buildSelectQuery(string $table, array $select, array $where, array $group, array $order = [], int $limit = 1, int $page = 0)
    {
        $isGroup = (count($group) < count($select));
        $query = '';
        if ($this->driver->jush() == "sql" && ($page) && ($limit) && ($group) && $isGroup) {
            $query = "SQL_CALC_FOUND_ROWS ";
        }
        $query .= \implode(", ", $select) . "\nFROM " . $this->table($table);
        $clauses = '';
        if (($where)) {
            $clauses = "\nWHERE " . \implode(" AND ", $where);
        }
        if (($group) && $isGroup) {
            $clauses .= "\nGROUP BY " . \implode(", ", $group);
        }
        if (($order)) {
            $clauses .= "\nORDER BY " . \implode(", ", $order);
        }
        $limit = $limit != "" ? +$limit : null;
        $offset = $page ? $limit * $page : 0;

        return "SELECT" . $this->limit($query, $clauses, $limit, $offset, "\n");
    }

    /**
     * @inheritDoc
     */
    public function applySqlFunction(string $function, string $column)
    {
        return ($function ? ($function == "unixepoch" ? "DATETIME($column, '$function')" :
            ($function == "count distinct" ? "COUNT(DISTINCT " : strtoupper("$function(")) . "$column)") : $column);
    }

    /**
     * @inheritDoc
     */
    public function defaultValue($field)
    {
        $default = $field->default;
        return ($default === null ? "" : " DEFAULT " .
            (preg_match('~char|binary|text|enum|set~', $field->type) ||
            preg_match('~^(?![a-z])~i', $default) ? $this->quote($default) : $default));
    }

    /**
     * @inheritDoc
     */
    public function limit(string $query, string $where, int $limit, int $offset = 0, string $separator = " ")
    {
        return "";
    }

    /**
     * @inheritDoc
     */
    public function limitToOne(string $table, string $query, string $where, string $separator = "\n")
    {
        return $this->limit($query, $where, 1, 0, $separator);
    }

    /**
     * @inheritDoc
     */
    public function convertField(TableFieldEntity $field)
    {
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
    public function convertFields(array $columns, array $fields, array $select = [])
    {
        $clause = "";
        foreach ($columns as $key => $val) {
            if ($select && !in_array($this->escapeId($key), $select)) {
                continue;
            }
            $as = $this->convertField($fields[$key]);
            if ($as) {
                $clause .= ", $as AS " . $this->escapeId($key);
            }
        }
        return $clause;
    }

    /**
     * @inheritDoc
     */
    public function countRowsSql(string $table, array $where, bool $isGroup, array $groups)
    {
        $query = " FROM " . $this->table($table) . ($where ? " WHERE " . implode(" AND ", $where) : "");
        return ($isGroup && ($this->driver->jush() == "sql" || count($groups) == 1)
            ? "SELECT COUNT(DISTINCT " . implode(", ", $groups) . ")$query"
            : "SELECT COUNT(*)" . ($isGroup ? " FROM (SELECT 1$query GROUP BY " . implode(", ", $groups) . ") x" : $query)
        );
    }

    /**
     * @inheritDoc
     */
    public function sqlForCreateTable(string $table, bool $autoIncrement, string $style)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function sqlForCreateIndex(string $table, string $type, string $name, string $columns)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function sqlForUseDatabase(string $database)
    {
        return "";
    }

    /**
     * @inheritDoc
     */
    public function sqlForForeignKeys(string $table)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function sqlForTruncateTable(string $table)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function sqlForCreateTrigger(string $table)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function autoIncrement()
    {
        return "";
    }
}
