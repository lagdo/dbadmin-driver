<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\DriverInterface;
use Lagdo\DbAdmin\Driver\Driver\GrammarInterface;
use Lagdo\DbAdmin\Driver\Entity\AbstractTableEntity;
use Lagdo\DbAdmin\Driver\Entity\ColumnEntity;
use Lagdo\DbAdmin\Driver\Entity\FieldType;
use Lagdo\DbAdmin\Driver\Entity\ForeignKeyEntity;
use Lagdo\DbAdmin\Driver\Entity\QueryEntity;
use Lagdo\DbAdmin\Driver\Entity\TableEntity;
use Lagdo\DbAdmin\Driver\Entity\TableSelectEntity;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Utils\Utils;

use function array_flip;
use function array_keys;
use function array_map;
use function count;
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
use function str_ireplace;
use function str_replace;
use function substr;
use function trim;

abstract class AbstractGrammar implements GrammarInterface
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
     * Formulate SQL modification query with limit 1
     *
     * @param string $table
     * @param string $query Everything after UPDATE or DELETE
     * @param string $where
     *
     * @return string
     */
    abstract protected function limitToOne(string $table, string $query, string $where): string;

    /**
     * @inheritDoc
     */
    protected function getLimitClause(string $query, string $where, int $limit, int $offset = 0): string
    {
        return match(true) {
            $limit <= 0 => " $query$where",
            $offset <= 0 => " $query$where LIMIT $limit",
            default => " $query$where LIMIT $limit OFFSET $offset",
        };
    }

    /**
     * @inheritDoc
     */
    public function buildSelectQuery(TableSelectEntity $select): string
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
    public function getSelectQuery(string $table, array $select, array $where, array $group = [],
        array $order = [], int $limit = 1, int $page = 0): string
    {
        $entity = new TableSelectEntity($table, $select,
            $where, $group, $order, $limit, $page);
        return $this->buildSelectQuery($entity);
    }

    /**
     * @inheritDoc
     */
    public function getInsertQuery(string $table, array $values): string
    {
        $table = $this->driver->escapeTableName($table);
        return empty($values) ? "INSERT INTO $table DEFAULT VALUES" :
            "INSERT INTO $table (" . implode(', ', array_keys($values)) .
                ') VALUES (' . implode(', ', $values) . ')';
    }

    /**
     * @inheritDoc
     */
    public function getUpdateQuery(string $table, array $values, string $queryWhere, int $limit = 0): string
    {
        $assignments = [];
        foreach ($values as $name => $value) {
            $assignments[] = "$name = $value";
        }
        $query = $this->driver->escapeTableName($table) . ' SET ' . implode(', ', $assignments);
        return $limit <= 0 ? "UPDATE $query $queryWhere" :
            'UPDATE' . $this->limitToOne($table, $query, $queryWhere);
    }

    /**
     * @inheritDoc
     */
    public function getDeleteQuery(string $table, string $queryWhere, int $limit = 0): string
    {
        $query = 'FROM ' . $this->driver->escapeTableName($table);
        return $limit <= 0 ? "DELETE $query $queryWhere" :
            'DELETE' . $this->limitToOne($table, $query, $queryWhere);
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

        $enumLength = $this->driver->enumLengthRegex();
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
    public function getFieldType(FieldType $field, string $collate = "COLLATE"): string
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
     * This is the process_field() function in Adminer.
     *
     * @inheritDoc
     */
    public function getFieldClauses(TableFieldEntity $field, TableFieldEntity $typeField): ColumnEntity
    {
        // MariaDB exports CURRENT_TIMESTAMP as a function.
        if ($field->onUpdate) {
            $field->onUpdate = str_ireplace("current_timestamp()", "CURRENT_TIMESTAMP", $field->onUpdate);
        }

        $column = new ColumnEntity($field);

        $column->name = $this->escapeId($field->name);
        $column->type = $this->getFieldType($typeField);
        $column->nullValue = $field->nullable ? ' NULL' : ' NOT NULL'; // NULL for timestamp
        $column->defaultValue = $this->getDefaultValueClause($field);
        if (preg_match('~timestamp|datetime~', $field->type) && $field->onUpdate) {
            $column->onUpdate = " ON UPDATE {$field->onUpdate}";
        }
        if ($this->driver->support('comment') && $field->comment !== '') {
            $column->comment = ' COMMENT ' . $this->driver->quote($field->comment);
        }
        $column->autoIncrement = $field->autoIncrement ? $this->getAutoIncrementModifier() : null;

        return $column;
    }

    /**
     * @param ForeignKeyEntity $foreignKey
     *
     * @return string
     */
    protected function formatForeignKey(ForeignKeyEntity $foreignKey): string
    {
        [$sources, $targets] = $this->fkFields($foreignKey);
        $onActions = $this->driver->actions();
        $query = "FOREIGN KEY ($sources) REFERENCES " . $this->fkTablePrefix($foreignKey) .
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
     * @param AbstractTableEntity $table
     * @param string $prefix
     *
     * @return array<string>
     */
    protected function getForeignKeyClauses(AbstractTableEntity $table, string $prefix = ''): array
    {
        return array_map(fn(ForeignKeyEntity $fkField) =>
            $prefix . $this->formatForeignKey($fkField), $table->foreignKeys);
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
        return !$this->setCharset ? '' : 'SET NAMES ' . $this->driver->charset() . ";\n\n";
    }

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
    public function unescapeId(string $idf): string
    {
        $last = substr($idf, -1);
        return str_replace($last . $last, $last, substr($idf, 1, -1));
    }

    /**
     * @inheritDoc
     */
    public function escapeTableName(string $idf): string
    {
        return $this->escapeId($idf);
    }

    /**
     * @inheritDoc
     */
    public function getRowCountQuery(string $table, array $where, bool $isGroup, array $groups): string
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
    public function convertField(TableFieldEntity $field): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function unconvertField(TableFieldEntity $field, string $value): string
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function getTableDefinitionQueries(string $table, bool $autoIncrement, string $style): string
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getIndexCreationQuery(string $table, string $type, string $name, string $columns): string
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
    public function getTriggerCreationQuery(string $table): string
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
        $parse = $this->driver->sqlStatementRegex();
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
    public function parseQueries(QueryEntity $queryEntity): bool
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
    public function getDefaultValueClause(TableFieldEntity $field): string
    {
        return match(true) {
            $field->default === null => '',
            preg_match('~char|binary|text|enum|set~', $field->type) > 0,
            preg_match('~^(?![a-z])~i', $field->default) > 0 =>
                ' DEFAULT ' . $this->driver->quote($field->default),
            default => " DEFAULT {$field->default}",
        };
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
    public function convertFields(array $columns, array $fields, array $select = []): string
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
