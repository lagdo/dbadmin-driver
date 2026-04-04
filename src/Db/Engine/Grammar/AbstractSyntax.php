<?php

namespace Lagdo\DbAdmin\Support\Db\Engine\Grammar;

use Lagdo\DbAdmin\Support\DriverInterface;
use Lagdo\DbAdmin\Support\Db\Admin\Grammar\SyntaxInterface;
use Lagdo\DbAdmin\Support\Dto\QueryDto;
use Lagdo\DbAdmin\Support\GrammarInterface;
use Lagdo\DbAdmin\Support\Utils\Utils;

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

abstract class AbstractSyntax implements SyntaxInterface
{
    /**
     * @var bool
     */
    protected $setCharset = false;

    /**
     * @param DriverInterface $driver
     * @param GrammarInterface $grammar
     * @param Utils $utils
     */
    public function __construct(protected DriverInterface $driver,
        protected GrammarInterface $grammar, protected Utils $utils)
    {}

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
        if (!preg_match('~^[`\'"[]~', $idf)) {
            return $idf;
        }
        $last = substr($idf, -1);
        return str_replace("{$last}{$last}", $last, substr($idf, 1, -1));
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
    public function removeDefiner(string $query): string
    {
        return preg_replace('~^([A-Z =]+) DEFINER=`' .
            preg_replace('~@(.*)~', '`@`(%|\1)', $this->driver->user()) .
            '`~', '\1', $query); //! proper escaping of user
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
     * @param QueryDto $queryDto
     *
     * @return bool
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
     * @param QueryDto $queryDto
     * @param string $found
     * @param array $match
     *
     * @return bool
     */
    private function notQuery(QueryDto $queryDto, string $found, array &$match)
    {
        return preg_match('(' . ($found == '/*' ? '\*/' : ($found == '[' ? ']' :
            (preg_match('~^-- |^#~', $found) ? "\n" : preg_quote($found) . "|\\\\."))) . '|$)s',
            $queryDto->queries, $match, PREG_OFFSET_CAPTURE, $queryDto->offset) > 0;
    }

    /**
     * @param QueryDto $queryDto
     * @param string $found
     *
     * @return void
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
     * @param QueryDto $queryDto
     *
     * @return int
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
