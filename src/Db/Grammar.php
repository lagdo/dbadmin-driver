<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Entity\TableSelectEntity;
use Lagdo\DbAdmin\Driver\Entity\ForeignKeyEntity;
use Lagdo\DbAdmin\Driver\Entity\QueryEntity;

use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\UtilInterface;
use Lagdo\DbAdmin\Driver\TranslatorInterface;

use function preg_match;
use function preg_quote;
use function substr;
use function strlen;
use function is_string;
use function trim;
use function rtrim;
use function intval;
use function implode;
use function array_map;

abstract class Grammar implements GrammarInterface
{
    use GrammarTrait;

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
     * The constructor
     *
     * @param DriverInterface $driver
     * @param UtilInterface $util
     * @param TranslatorInterface $trans
     */
    public function __construct(DriverInterface $driver, UtilInterface $util, TranslatorInterface $trans)
    {
        $this->driver = $driver;
        $this->util = $util;
        $this->trans = $trans;
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
     * @param ForeignKeyEntity $foreignKey
     *
     * @return array
     */
    private function fkFields(ForeignKeyEntity $foreignKey)
    {
        $escape = function ($idf) { return $this->escapeId($idf); };
        return [
            implode(', ', array_map($escape, $foreignKey->source)),
            implode(', ', array_map($escape, $foreignKey->target)),
        ];
    }

    /**
     * @param ForeignKeyEntity $foreignKey
     *
     * @return string
     */
    private function fkTablePrefix(ForeignKeyEntity $foreignKey)
    {
        $prefix = '';
        if ($foreignKey->database !== '' && $foreignKey->database !== $this->driver->database()) {
            $prefix .= $this->escapeId($foreignKey->database) . '.';
        }
        if ($foreignKey->schema !== '' && $foreignKey->schema !== $this->driver->schema()) {
            $prefix .= $this->escapeId($foreignKey->schema) . '.';
        }
        return $prefix;
    }

    /**
     * @inheritDoc
     */
    public function formatForeignKey(ForeignKeyEntity $foreignKey)
    {
        [$sources, $targets] = $this->fkFields($foreignKey);
        $onActions = $this->driver->actions();
        $query = " FOREIGN KEY ($sources) REFERENCES " . $this->fkTablePrefix($foreignKey) .
            $this->table($foreignKey->table) . " ($targets)";
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

        return 'SELECT' . $this->limit($query, $select->clauses, $limit, $offset);
    }

    /**
     * @inheritDoc
     */
    public function defaultValue($field)
    {
        $default = $field->default;
        return ($default === null ? '' : ' DEFAULT ' .
            (preg_match('~char|binary|text|enum|set~', $field->type) ||
            preg_match('~^(?![a-z])~i', $default) ? $this->driver->quote($default) : $default));
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
    public function sqlForRowCount(string $table, array $where, bool $isGroup, array $groups)
    {
        $query = ' FROM ' . $this->table($table);
        if (!empty($where)) {
            $query .= ' WHERE ' . implode(' AND ', $where);
        }
        return ($isGroup && ($this->driver->jush() == 'sql' || count($groups) == 1) ?
            'SELECT COUNT(DISTINCT ' . implode(', ', $groups) . ")$query" :
            'SELECT COUNT(*)' . ($isGroup ? " FROM (SELECT 1$query GROUP BY " .
            implode(', ', $groups) . ') x' : $query)
        );
    }

    /**
     * @param QueryEntity $queryEntity
     *
     * @return bool
     */
    private function setDelimiter(QueryEntity $queryEntity)
    {
        $space = "(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";
        if ($queryEntity->offset !== 0 ||
            !preg_match("~^$space*+DELIMITER\\s+(\\S+)~i", $queryEntity->queries, $match)) {
            return false;
        }
        $queryEntity->delimiter = $match[1];
        $queryEntity->queries = substr($queryEntity->queries, strlen($match[0]));
        return true;
    }

    /**
     * Return the regular expression for queries
     *
     * @return string
     */
    abstract protected function queryRegex();
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
     * @param QueryEntity $queryEntity
     * @param string $found
     * @param array $match
     *
     * @return bool
     */
    private function notQuery(QueryEntity $queryEntity, string $found, array &$match)
    {
        return preg_match('(' . ($found == '/*' ? '\*/' : ($found == '[' ? ']' :
            (preg_match('~^-- |^#~', $found) ? "\n" : preg_quote($found) . "|\\\\."))) . '|$)s',
            $queryEntity->queries, $match, PREG_OFFSET_CAPTURE, $queryEntity->offset) > 0;
    }

    /**
     * @param QueryEntity $queryEntity
     * @param string $found
     *
     * @return void
     */
    private function skipComments(QueryEntity $queryEntity, string $found)
    {
        // Find matching quote or comment end
        $match = [];
        while ($this->notQuery($queryEntity, $found, $match)) {
            //! Respect sql_mode NO_BACKSLASH_ESCAPES
            $s = $match[0][0];
            $queryEntity->offset = $match[0][1] + strlen($s);
            if ($s[0] != "\\") {
                break;
            }
        }
    }

    /**
     * @param QueryEntity $queryEntity
     *
     * @return int
     */
    private function nextQueryPos(QueryEntity $queryEntity)
    {
        // TODO: Move this to driver implementations
        $parse = $this->queryRegex();
        $delimiter = preg_quote($queryEntity->delimiter);
        // Should always match
        preg_match("($delimiter$parse)", $queryEntity->queries, $match,
            PREG_OFFSET_CAPTURE, $queryEntity->offset);
        [$found, $pos] = $match[0];
        if (!is_string($found) && $queryEntity->queries == '') {
            return -1;
        }
        $queryEntity->offset = $pos + strlen($found);
        if (empty($found) || rtrim($found) == $queryEntity->delimiter) {
            return intval($pos);
        }
        // Find matching quote or comment end
        $this->skipComments($queryEntity, $found);
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function parseQueries(QueryEntity $queryEntity)
    {
        $queryEntity->queries = trim($queryEntity->queries);
        while ($queryEntity->queries !== '') {
            if ($this->setDelimiter($queryEntity)) {
                continue;
            }
            $pos = $this->nextQueryPos($queryEntity);
            if ($pos < 0) {
                return false;
            }
            if ($pos === 0) {
                continue;
            }
            // End of a query
            $queryEntity->query = substr($queryEntity->queries, 0, $pos);
            $queryEntity->queries = substr($queryEntity->queries, $queryEntity->offset);
            $queryEntity->offset = 0;
            return true;
        }
        return false;
    }
}
