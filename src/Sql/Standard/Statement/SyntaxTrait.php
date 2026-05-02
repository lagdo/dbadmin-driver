<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Statement;

use Lagdo\DbAdmin\Driver\Sql\DbProxyTrait;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\QueryInputDto;

use function array_flip;
use function implode;
use function intval;
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
     * @param QueryInputDto $input
     *
     * @return bool
     */
    private function setDelimiter(QueryInputDto $input)
    {
        $space = "(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";
        if ($input->offset !== 0 ||
            !preg_match("~^$space*+DELIMITER\\s+(\\S+)~i", $input->queries, $match)) {
            return false;
        }

        $input->delimiter = $match[1];
        $input->queries = substr($input->queries, strlen($match[0]));
        return true;
    }

    /**
     * @param QueryInputDto $input
     * @param string $found
     * @param array $match
     *
     * @return bool
     */
    private function notQuery(QueryInputDto $input, string $found, array &$match)
    {
        return preg_match('(' . ($found == '/*' ? '\*/' : ($found == '[' ? ']' :
            (preg_match('~^-- |^#~', $found) ? "\n" : preg_quote($found) . "|\\\\."))) . '|$)s',
            $input->queries, $match, PREG_OFFSET_CAPTURE, $input->offset) > 0;
    }

    /**
     * @param QueryInputDto $input
     * @param string $found
     *
     * @return void
     */
    private function skipComments(QueryInputDto $input, string $found)
    {
        // Find matching quote or comment end
        $match = [];
        while ($this->notQuery($input, $found, $match)) {
            //! Respect sql_mode NO_BACKSLASH_ESCAPES
            $s = $match[0][0];
            $input->offset = $match[0][1] + strlen($s);
            if (($s[0] ?? '') != "\\") {
                break;
            }
        }
    }

    /**
     * @param QueryInputDto $input
     *
     * @return int
     */
    private function nextQueryPos(QueryInputDto $input)
    {
        // TODO: Move this to driver implementations
        $parse = $this->_engine()->sqlStatementRegex();
        $delimiter = preg_quote($input->delimiter);
        // Should always match
        preg_match("($delimiter$parse)", $input->queries, $match,
            PREG_OFFSET_CAPTURE, $input->offset);
        [$found, $pos] = $match[0];
        if (!is_string($found) && $input->queries == '') {
            return -1;
        }
        $input->offset = $pos + strlen($found);
        if (empty($found) || rtrim($found) == $input->delimiter) {
            return intval($pos);
        }

        // Find matching quote or comment end
        $this->skipComments($input, $found);
        return 0;
    }

    /**
     * Parse a string containing SQL queries
     *
     * @param QueryInputDto $input
     *
     * @return bool
     */
    public function parseQueries(QueryInputDto $input): bool
    {
        $input->queries = trim($input->queries);
        while ($input->queries !== '') {
            if ($this->setDelimiter($input)) {
                continue;
            }
            $pos = $this->nextQueryPos($input);
            if ($pos < 0) {
                return false;
            }
            if ($pos === 0) {
                continue;
            }

            // End of a query
            $input->query = substr($input->queries, 0, $pos);
            $input->queries = substr($input->queries, $input->offset);
            $input->offset = 0;
            return true;
        }
        return false;
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
}
