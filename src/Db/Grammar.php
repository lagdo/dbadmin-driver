<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\Driver\GrammarInterface;
use Lagdo\DbAdmin\Driver\Entity\FieldType;
use Lagdo\DbAdmin\Driver\Entity\ForeignKeyEntity;
use Lagdo\DbAdmin\Driver\Entity\QueryEntity;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\Entity\TableSelectEntity;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Utils\Utils;

use function array_flip;
use function array_map;
use function implode;
use function intval;
use function in_array;
use function is_string;
use function preg_match;
use function preg_match_all;
use function preg_quote;
use function preg_replace;
use function rtrim;
use function strlen;
use function strtr;
use function str_replace;
use function substr;
use function trim;

abstract class Grammar implements GrammarInterface
{
    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @var bool
     */
    protected $setCharset = false;

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

        return 'SELECT' . $this->getLimitClause($query,
            $select->clauses, $limit, $offset);
    }

    /**
     * @inheritDoc
     */
    public function getLimitClause(string $query, string $where, int $limit, int $offset = 0)
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
        $escape = fn(string $idf): string => $this->escapeId($idf);
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
        if ($foreignKey->database !== '' && $foreignKey->database !== $this->database()) {
            $prefix .= $this->escapeId($foreignKey->database) . '.';
        }
        if ($foreignKey->schema !== '' && $foreignKey->schema !== $this->schema()) {
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
            $this->escapeTableName($foreignKey->table) . " ($targets)";
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
    public function bracketEscape(string $idf, bool $back = false): string
    {
        // escape brackets inside name='x[]'
        static $trans = [':' => ':1', ']' => ':2', '[' => ':3', '"' => ':4'];
        return strtr($idf, $back ? array_flip($trans) : $trans);
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
     */
    public function processLength(string $length): string
    {
        if (!$length) {
            return '';
        }

        $enumLength = $this->enumLength();
        $pattern = "~^\\s*\\(?\\s*$enumLength(?:\\s*,\\s*$enumLength)*+\\s*\\)?\\s*\$~";
        if (preg_match($pattern, $length) &&
            preg_match_all("~$enumLength~", $length, $matches)) {
            return '(' . implode(',', $matches[0]) . ')';
        }
        return preg_replace('~^[0-9].*~', '(\0)', preg_replace('~[^-0-9,+()[\]]~', '', $length));
    }

    /**
     * Create SQL string from field type
     *
     * @param FieldType $field
     */
    public function processType(FieldType $field, string $collate = "COLLATE"): string
    {
        $length = $this->processLength($field->length);
        $type = preg_match($this->driver->numberRegex(), $field->type) &&
            in_array($field->unsigned, $this->driver->unsigned()) ?
            " {$field->unsigned}" : "";
        $collation = preg_match('~char|text|enum|set~', $field->type) &&
            $field->collation ? " $collate " . ($this->driver->jush() === 'mssql' ?
                $field->collation : $this->driver->quote($field->collation)) : "";
        return " {$field->type}{$length}{$type}{$collation}";
    }

    /**
     * @inheritDoc
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

    /**
     * @inheritDoc
     */
    public function setUtf8mb4(string $create): void
    {
        // possible false positive
        if (!$this->setCharset && preg_match('~\butf8mb4~i', $create)) {
            $this->setCharset = true;
        }
    }

    /**
     * @inheritDoc
     */
    public function getCharsetQuery(): string
    {
        return !$this->setCharset ? '' : 'SET NAMES ' . $this->charset() . ";\n\n";
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
    public function escapeTableName(string $idf)
    {
        return $this->escapeId($idf);
    }

    /**
     * @inheritDoc
     */
    public function getRowCountQuery(string $table, array $where, bool $isGroup, array $groups)
    {
        $query = ' FROM ' . $this->escapeTableName($table);
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
            if (($s[0] ?? '') != "\\") {
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

    /**
     * @inheritDoc
     */
    public function getDefaultValueClause(TableFieldEntity $field)
    {
        $default = $field->default;
        // Todo: use match
        return $default === null ? '' : ' DEFAULT ' .
            (preg_match('~char|binary|text|enum|set~', $field->type) ||
            preg_match('~^(?![a-z])~i', $default) ?
                $this->driver->quote($default) : $default);
    }

    /**
     * @inheritDoc
     */
    public function removeDefiner(string $query): string
    {
        return preg_replace('~^([A-Z =]+) DEFINER=`' .
            preg_replace('~@(.*)~', '`@`(%|\1)', $this->driver->user()) .
            '`~', '\1', $query); //! proper escaping of user
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
                $clause .= ", $as AS " . $this->escapeId($key);
            }
        }
        return $clause;
    }
}
