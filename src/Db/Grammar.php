<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Entity\TableSelectEntity;
use Lagdo\DbAdmin\Driver\Entity\ForeignKeyEntity;

use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\UtilInterface;
use Lagdo\DbAdmin\Driver\TranslatorInterface;

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
    public function limit(string $query, string $where, int $limit, int $offset = 0)
    {
        $sql = " $query$where";
        if ($limit > 0) {
            $sql .= " LIMIT $limit";
            if ($offset > 0) {
                $sql .= " OFFSET $offset";
            }
        }
        return $sql;
    }

    /**
     * @inheritDoc
     */
    public function formatForeignKey(ForeignKeyEntity $foreignKey)
    {
        $database = $foreignKey->database;
        $schema = $foreignKey->schema;
        $onActions = $this->driver->actions();
        $sources = implode(', ', array_map(function ($idf) {
            return $this->escapeId($idf);
        }, $foreignKey->source));
        $targets = implode(', ', array_map(function ($idf) {
            return $this->escapeId($idf);
        }, $foreignKey->target));

        $query = " FOREIGN KEY ($sources) REFERENCES ";
        if ($database != '' && $database != $this->driver->database()) {
            $query .= $this->escapeId($database) . '.';
        }
        if ($schema != '' && $schema != $this->driver->schema()) {
            $query .= $this->escapeId($schema) . '.';
        }
        $query .= $this->table($foreignKey->table) . " ($targets)";
        if (preg_match("~^($onActions)\$~", $foreignKey->onDelete)) {
            $query .= " ON DELETE {$foreignKey->onDelete}";
        }
        if (preg_match("~^($onActions)\$~", $foreignKey->onUpdate)) {
            $query .= " ON UPDATE {$foreignKey->onUpdate}";
        }

        return $query;
    }

    /**
     * @inheritDoc
     */
    public function buildSelectQuery(TableSelectEntity $select)
    {
        $query = \implode(', ', $select->fields) . ' FROM ' . $this->table($select->table);
        $limit = +$select->limit;
        $offset = $select->page ? $limit * $select->page : 0;

        return 'SELECT' . $this->limit($query, $select->clauses, $limit, $offset, "\n");
    }

    /**
     * @inheritDoc
     */
    public function defaultValue($field)
    {
        $default = $field->default;
        return ($default === null ? '' : ' DEFAULT ' .
            (preg_match('~char|binary|text|enum|set~', $field->type) ||
            preg_match('~^(?![a-z])~i', $default) ? $this->connection->quote($default) : $default));
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
    public function convertFields(array $columns, array $fields, array $select = [])
    {
        $clause = '';
        foreach ($columns as $key => $val) {
            if (!empty($select) && !in_array($this->escapeId($key), $select)) {
                continue;
            }
            $as = $this->convertField($fields[$key]);
            if ($as) {
                $clause .= ', $as AS ' . $this->escapeId($key);
            }
        }
        return $clause;
    }

    /**
     * @inheritDoc
     */
    public function countRowsSql(string $table, array $where, bool $isGroup, array $groups)
    {
        $query = ' FROM ' . $this->table($table) . ($where ? ' WHERE ' . implode(' AND ', $where) : '');
        return ($isGroup && ($this->driver->jush() == 'sql' || count($groups) == 1)
            ? 'SELECT COUNT(DISTINCT ' . implode(', ', $groups) . ")$query"
            : 'SELECT COUNT(*)' . ($isGroup ? " FROM (SELECT 1$query GROUP BY " . implode(', ', $groups) . ') x' : $query)
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
        return '';
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
        return '';
    }
}
