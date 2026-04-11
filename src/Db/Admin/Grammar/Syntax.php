<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Grammar;

use Lagdo\DbAdmin\Support\Db\AbstractDelegate;
use Lagdo\DbAdmin\Support\Dto\QueryDto;

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

class Syntax extends AbstractDelegate implements SyntaxInterface
{
    /**
     * @inheritDoc
     */
    public function escapeTableName(string $idf): string
    {
        return $this->grammar->escapeId($idf);
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
            preg_quote($this->grammar->escapeId('_'))) . ')([ \w)]+)$)', $key, $match)) {
            //! columns looking like functions
            return $match[1] . $this->grammar->escapeId($this->grammar->unescapeId($match[2])) .
                $match[3]; //! SQL injection
        }
        return $this->grammar->escapeId($key);
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

        $enumLength = $this->driver->enumLengthRegex();
        $pattern = "~^\\s*\\(?\\s*$enumLength(?:\\s*,\\s*$enumLength)*+\\s*\\)?\\s*\$~";
        if (preg_match($pattern, $length) &&
            preg_match_all("~$enumLength~", $length, $matches)) {
            return '(' . implode(',', $matches[0]) . ')';
        }
        return preg_replace('~^[0-9].*~', '(\0)', preg_replace('~[^-0-9,+()[\]]~', '', $length));
    }

    /**
     * @inheritDoc
     */
    private function setDelimiter(QueryDto $queryDto)
    {
        $space = "(?:\\s|/\\*[\s\S]*?\\*/|(?:#|-- )[^\n]*\n?|--\r?\n)";
        if ($queryDto->offset !== 0 ||
            !preg_match("~^$space*+DELIMITER\\s+(\\S+)~i", $queryDto->queries, $match)) {
            return false;
        }
        $queryDto->delimiter = $match[1];
        $queryDto->queries = substr($queryDto->queries, strlen($match[0]));
        return true;
    }

    /**
     * @inheritDoc
     */
    private function notQuery(QueryDto $queryDto, string $found, array &$match)
    {
        return preg_match('(' . ($found == '/*' ? '\*/' : ($found == '[' ? ']' :
            (preg_match('~^-- |^#~', $found) ? "\n" : preg_quote($found) . "|\\\\."))) . '|$)s',
            $queryDto->queries, $match, PREG_OFFSET_CAPTURE, $queryDto->offset) > 0;
    }

    /**
     * @inheritDoc
     */
    private function skipComments(QueryDto $queryDto, string $found)
    {
        // Find matching quote or comment end
        $match = [];
        while ($this->notQuery($queryDto, $found, $match)) {
            //! Respect sql_mode NO_BACKSLASH_ESCAPES
            $s = $match[0][0];
            $queryDto->offset = $match[0][1] + strlen($s);
            if (($s[0] ?? '') != "\\") {
                break;
            }
        }
    }

    /**
     * @inheritDoc
     */
    private function nextQueryPos(QueryDto $queryDto)
    {
        // TODO: Move this to driver implementations
        $parse = $this->driver->sqlStatementRegex();
        $delimiter = preg_quote($queryDto->delimiter);
        // Should always match
        preg_match("($delimiter$parse)", $queryDto->queries, $match,
            PREG_OFFSET_CAPTURE, $queryDto->offset);
        [$found, $pos] = $match[0];
        if (!is_string($found) && $queryDto->queries == '') {
            return -1;
        }
        $queryDto->offset = $pos + strlen($found);
        if (empty($found) || rtrim($found) == $queryDto->delimiter) {
            return intval($pos);
        }
        // Find matching quote or comment end
        $this->skipComments($queryDto, $found);
        return 0;
    }

    /**
     * @inheritDoc
     */
    public function parseQueries(QueryDto $queryDto): bool
    {
        $queryDto->queries = trim($queryDto->queries);
        while ($queryDto->queries !== '') {
            if ($this->setDelimiter($queryDto)) {
                continue;
            }
            $pos = $this->nextQueryPos($queryDto);
            if ($pos < 0) {
                return false;
            }
            if ($pos === 0) {
                continue;
            }
            // End of a query
            $queryDto->query = substr($queryDto->queries, 0, $pos);
            $queryDto->queries = substr($queryDto->queries, $queryDto->offset);
            $queryDto->offset = 0;
            return true;
        }
        return false;
    }
}
