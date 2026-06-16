<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Statement;

use Lagdo\DbAdmin\Driver\Sql\DbProxyTrait;
use Lagdo\DbAdmin\Driver\Sql\Dto\QueryCodeDto;
use Generator;

use function count;
use function implode;
use function preg_match;
use function preg_replace_callback;
use function strlen;
use function strpos;
use function str_repeat;
use function substr;
use function substr_replace;
use function trim;

trait SplitterTrait
{
    use DbProxyTrait;

    /**
     * @var string
     */
    private string $queryRegex = "(?:[^'\"`]*?(?:'[^'\"`]*'|\"[^'\"`]*\"|`[^'\"`]*`))";

    /**
     * @param QueryCodeDto $dto
     * @param string $query
     *
     * @return bool
     */
    private function setDelimiter(QueryCodeDto $dto, string $query): bool
    {
        // $copyRegex = "~^(\\s*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i";
        // if ($this->_engine()->pgsql() && preg_match($copyRegex, $query, $matches)) {
        //     $dto->delimiters = ['' => "\n\\\\\\.\r?\n"];
        //     return;
        // }

        $delimiterRegex = "~^\\s*+DELIMITER\\s+(\\S+)~i";
        if (preg_match($delimiterRegex, $query, $matches)) {
            $dto->delimiter = $matches[1];
            // $dto->delimiters = [$matches[1] => preg_quote($matches[1])];
            return true;
        }

        return false;
    }

    /**
     * @param QueryCodeDto $dto
     *
     * @return int
     */
    private function findEndOfQuery(QueryCodeDto $dto): int
    {
        $offset = strpos($dto->inputLine, $dto->delimiter);
        return $offset === false ? 0 : $offset + strlen($dto->delimiter);
    }

    /**
     * Return the position after the delimiter, or 0.
     *
     * @param QueryCodeDto $dto
     * @param string $delimiter
     * @param bool $withLength
     *
     * @return int|null
     */
    private function findDelimiterPosition(QueryCodeDto $dto,
        string $delimiter, bool $withLength): int|null
    {
        $regex = "/^{$this->queryRegex}*[^'\"`]*\$/s";
        $offset = 0;
        $delimiterLength = strlen($delimiter);

        // Todo: can this be done with a single regex?
        while (($offset = strpos($dto->inputLine, $delimiter, $offset)) !== false) {
            // Take only the delimiters not enclosed into quotes or double quotes.
            if (preg_match($regex, substr($dto->inputLine, 0, $offset), $matches)) {
                return $withLength ? $offset + $delimiterLength : $offset;
            }

            $offset += $delimiterLength;
        }

        return null;
    }

    /**
     * @param QueryCodeDto $dto
     * @param int $offset
     *
     * @return void
     */
    private function truncateStartOfLine(QueryCodeDto $dto, int $offset): void
    {
        $dto->queryLine = substr($dto->queryLine, $offset);
        $dto->inputLine = substr($dto->inputLine, $offset);
    }

    /**
     * @param QueryCodeDto $dto
     * @param int $offset
     *
     * @return void
     */
    private function truncateEndOfLine(QueryCodeDto $dto, int $offset): void
    {
        $dto->queryLine = substr($dto->queryLine, 0, $offset);
        $dto->inputLine = substr($dto->inputLine, 0, $offset);
    }

    /**
     * @param QueryCodeDto $dto
     *
     * @return void
     */
    private function parseEndOfLine(QueryCodeDto $dto): void
    {
        // Find the start of comment or multiline string.
        $regex = "/('|--|\/\*|#)/s";
        $flags = PREG_OFFSET_CAPTURE;
        $found = preg_match($regex, $dto->inputLine, $matches, $flags);
        // Nothing found.
        if (!$found) {
            return;
        }

        $delimiter = $matches[0][0];
        $offset = $matches[0][1];

        // End of multiline string found.
        if ($delimiter === "'") {
            // Switch the string mode.
            $dto->inMultilineString = true;

            // Mask the end of the line.
            $length = strlen($dto->inputLine) - $offset;
            $spaces = str_repeat(' ', $length);
            $dto->inputLine = substr_replace($dto->inputLine, $spaces, $offset, $length);

            return;
        }

        // Comment found.
        if ($delimiter === '/*') {
            // Switch the comment mode.
            $dto->inMultilineComment = true;
        }
        // Truncate the end of the line.
        $this->truncateEndOfLine($dto, $offset);
    }

    /**
     * @param QueryCodeDto $dto
     *
     * @return bool
     */
    private function parseMultiLineCommentEnd(QueryCodeDto $dto): bool
    {
        if (!$dto->inMultilineComment) {
            return true;
        }

        // Find the end of comment delimiter.
        $offset = $this->findDelimiterPosition($dto, '*/', true);
        if ($offset === null) {
            // Middle of a multiline comment. Skip the line.
            return false;
        }

        // Last line of a multiline comment. Truncate the start.
        $this->truncateStartOfLine($dto, $offset);

        // Switch the comment mode.
        $dto->inMultilineComment = false;

        return true;
    }

    /**
     * @param QueryCodeDto $dto
     * @param string $line
     * @param bool $newLine
     *
     * @return void
     */
    private function addLineToQueryBuffer(QueryCodeDto $dto, string $line, bool $newLine): void
    {
        $dto->queryLines[] = $line;
        if ($newLine) {
           $dto->queryLines[] = "\n"; 
        }
    }

    /**
     * @param QueryCodeDto $dto
     *
     * @return bool
     */
    private function parseMultiLineStringEnd(QueryCodeDto $dto): bool
    {
        if (!$dto->inMultilineString) {
            return true;
        }

        $regex = "/('\s*)/s";
        $flags = PREG_OFFSET_CAPTURE;
        $found = preg_match($regex, $dto->inputLine, $matches, $flags);
        if (!$found) {
            // Middle of a multiline string. Add the line to the buffer.
            $this->addLineToQueryBuffer($dto, $dto->queryLine, true);
            return false;
        }

        $offset = $matches[1][1] + strlen($matches[1][0]);
        // Last line of a multiline string.
        // Copy the start of the line to the buffer.
        $this->addLineToQueryBuffer($dto, substr($dto->queryLine, 0, $offset), false);
        // Truncate the start of the line.
        $this->truncateStartOfLine($dto, $offset);

        // Switch the string mode.
        $dto->inMultilineString = false;

        return true;
    }

    /**
     * Callback for preg_replace_callback.
     *
     * @param array $matches
     *
     * @return string
     */
    private function replaceMatchWithSpaces(array $matches): string
    {
        // Replace the matched string with same length spaces.
        return str_repeat(' ', strlen($matches[0]));
    }

    /**
     * @param QueryCodeDto $dto
     *
     * @return bool
     */
    private function prepareQueryLine(QueryCodeDto $dto): bool
    {
        if (!$this->parseMultiLineStringEnd($dto)) {
            return false;
        }

        if (!$this->parseMultiLineCommentEnd($dto)) {
            return false;
        }

        // Mask quoted strings.
        $callback = $this->replaceMatchWithSpaces(...);
        $regexes = [
            "'[^']*'",
            "`[^`]*`",
            "\"[^\"]*\"",
        ];
        $regex = implode('|', $regexes);
        $dto->inputLine = preg_replace_callback("/$regex/s", $callback, $dto->inputLine);
        // foreach ($regexes as $regex) {
        //     $dto->inputLine = preg_replace_callback("/$regex/s", $callback, $dto->inputLine);
        // }

        $this->parseEndOfLine($dto);

        return true;
    }

    /**
     * @param QueryCodeDto $dto
     *
     * @return bool
     */
    private function getQueryLine(QueryCodeDto $dto): bool
    {
        if (!($dto->queryLineReader)($dto)) {
            return false;
        }

        $dto->inputLine = $dto->queryLine;
        // Mask comments in "/*  */", and antislashed quotes and double quotes.
        $callback = $this->replaceMatchWithSpaces(...);
        $regexes = [
            "\/\*.*?\*\/",
            "''",
            "\\\\\\\\",
            "\\\\'",
            "\\\\`",
            "\\\\\"",
        ];
        $regex = implode('|', $regexes);
        $dto->inputLine = preg_replace_callback("/$regex/s", $callback, $dto->inputLine);
        // foreach ($regexes as $regex) {
        //     $dto->inputLine = preg_replace_callback("/$regex/s", $callback, $dto->inputLine);
        // }

        return true;
    }

    /**
     * Split a string or a file containing SQL queries.
     *
     * @param QueryCodeDto $dto
     *
     * @return Generator
     */
    public function splitQueries(QueryCodeDto $dto): Generator
    {
        while ($this->getQueryLine($dto)) {
            if (!$this->prepareQueryLine($dto)) {
                continue;
            }

            // The line might be truncated, so don't process anything without a delimiter after.
            while (($offset = $this->findEndOfQuery($dto)) > 0) {
                $this->addLineToQueryBuffer($dto, substr($dto->queryLine, 0, $offset), true);
                $this->truncateStartOfLine($dto, $offset);

                // Return the query for processing.
                $query = implode('', $dto->queryLines);
                $dto->queryLines = [];

                // Yield the query if it is not a delimiter.
                if (!$this->setDelimiter($dto, $query)) {
                    yield $query;
                }
            }

            // Add the remaining input line to the query buffer, if there is any.
            if (trim($dto->queryLine) !== '') {
                $this->addLineToQueryBuffer($dto, $dto->queryLine, true);
            }
        }

        if (count($dto->queryLines) > 0) {
            $query = implode('', $dto->queryLines);
            $dto->queryLines = [];

            if (!$this->setDelimiter($dto, $query)) {
                yield $query;
            }
        }
    }
}
