<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Statement;

use Lagdo\DbAdmin\Driver\Sql\DbProxyTrait;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnType;

use function array_flip;
use function implode;
use function in_array;
use function preg_match;
use function preg_match_all;
use function preg_quote;
use function preg_replace;
use function strtr;
use function str_replace;
use function trim;

trait SyntaxTrait
{
    use DbProxyTrait;

    /**
     * Get escaped table name
     *
     * @param string $idf
     *
     * @return string
     */
    public function escapeTableName(string $idf): string
    {
        return $this->_statement()->escapeId($idf);
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
        return strtr($idf, $back ? array_flip($trans) : $trans);
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
            preg_quote($this->_statement()->escapeId('_'))) . ')([ \w)]+)$)', $key, $match)) {
            //! columns looking like functions
            $expr = $this->_statement()->escapeId($this->_statement()->unescapeId($match[2]));
            return "{$match[1]}{$expr}{$match[3]}"; //! SQL injection
        }

        return $this->_statement()->escapeId($key);
    }

    /**
     * Remove current user definer from SQL command
     *
     * @param string $query
     *
     * @return string
     */
    public function removeDefiner(string $query): string
    {
        return preg_replace('~^([A-Z =]+) DEFINER=`' .
            preg_replace('~@(.*)~', '`@`(%|\1)', $this->_engine()->user()) .
            '`~', '\1', $query); //! proper escaping of user
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

        $enumLength = $this->_engine()->enumLengthRegex();
        $pattern = "~^\\s*\\(?\\s*$enumLength(?:\\s*,\\s*$enumLength)*+\\s*\\)?\\s*\$~";
        if (preg_match($pattern, $length) &&
            preg_match_all("~$enumLength~", $length, $matches)) {
            return '(' . implode(',', $matches[0]) . ')';
        }
        return preg_replace('~^[0-9].*~', '(\0)', preg_replace('~[^-0-9,+()[\]]~', '', $length));
    }

    /**
     * @param ColumnDto $column
     * @param string $value
     * @param string $function
     *
     * @return string
     */
    private function getInputFieldExpression(ColumnDto $column,
        string $value, string $function): string
    {
        $columnName = $this->_statement()->escapeId($column->name);
        $expression = $this->_engine()->quote($value);

        return match(true) {
            preg_match('~^(now|getdate|uuid)$~', $function) => "$function()",
            preg_match('~^current_(date|timestamp)$~', $function) => $function,
            preg_match('~^([+-]|\|\|)$~', $function) => "$columnName $function $expression",
            preg_match('~^[+-] interval$~', $function) => "$columnName $function " .
                (preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i", $value) &&
                    !$this->_engine()->pgsql() ? $value : $expression),
            preg_match('~^(addtime|subtime|concat)$~', $function) =>
                "$function($columnName, $expression)",
            preg_match('~^(md5|sha1|password|encrypt)$~', $function) => "$function($expression)",
            default => $expression,
        };
    }

    /**
     * @param ColumnDto $column Single column from columns()
     * @param string $value
     * @param string $function
     *
     * @return string
     */
    public function getUnconvertedFieldValue(ColumnDto $column,
        string $value, string $function = ''): string
    {
        if ($function === 'SQL') {
            return $value; // SQL injection
        }

        $expression = $this->getInputFieldExpression($column, $value, $function);
        return $this->_statement()->unconvertColumn($column, $expression);
    }

    /**
     * Create SQL string from column type
     *
     * @param ColumnType $column
     *
     * @return string
     */
    public function getColumnType(ColumnType $column, string $collate = "COLLATE"): string
    {
        $type = trim($column->type);
        $length = $this->_statement()->processLength($column->length);
        $typeInfo = preg_match($this->_engine()->numberRegex(), $type) &&
            in_array($column->unsigned, $this->_engine()->unsigned()) ?
            " {$column->unsigned}" : "";
        $collation = preg_match('~char|text|enum|set~', $type) && $column->collation ?
            " $collate " . ($this->_engine()->mssql() ? $column->collation :
                $this->_engine()->quote($column->collation)) : "";
        return " {$type}{$length}{$typeInfo}{$collation}";
    }
}
