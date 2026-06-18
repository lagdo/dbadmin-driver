<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Statement;

use Lagdo\DbAdmin\Driver\Sql\DbProxyTrait;
use Lagdo\DbAdmin\Driver\Sql\Dto\QueryCodeDto;
use Generator;

use function implode;
use function preg_match;
use function preg_replace_callback;
use function strlen;
use function strpos;
use function strtoupper;
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
     * @var string
     */
    private string $functionDelimiterRegex = "\\$[a-z0-9]*?\\$";

    /**
     * @param QueryCodeDto $dto
     *
     * @return string
     */
    private function getBufferedQuery(QueryCodeDto $dto): string
    {
        $query = trim(implode('', $dto->queryLines));
        $dto->queryLines = [];

        return $query;
    }

    /**
     * @param QueryCodeDto $dto
     *
     * @return int|null
     */
    private function findEndOfQuery(QueryCodeDto $dto): int|null
    {
        $offset = strpos($dto->inputLine, $dto->queryDelimiter);
        return $offset === false ? null : $offset;
    }

    /**
     * Return the delimiter position, or null.
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

        // Last line of a multiline comment. Mask the start.
        $this->maskStartOfLine($dto, $offset);

        // Switch the comment mode.
        $dto->inMultilineComment = false;

        return true;
    }

    /**
     * @param QueryCodeDto $dto
     *
     * @return void
     */
    private function bufferLineContent(QueryCodeDto $dto): void
    {
        if ($dto->queryLine !== '') {
            $dto->queryLines[] = $dto->queryLine;
            $dto->queryLine = '';
        }
    }

    /**
     * @param QueryCodeDto $dto
     * @param int $offset
     *
     * @return void
     */
    private function bufferEndOfQuery(QueryCodeDto $dto, int $offset): void
    {
        // Copy the start of the line to the buffer.
        $dto->queryLines[] = substr($dto->queryLine, 0, $offset);
        // Truncate the start of the line.
        $offset += strlen($dto->queryDelimiter);
        $dto->queryLine = substr($dto->queryLine, $offset);
        $dto->inputLine = substr($dto->inputLine, $offset);
    }

    /**
     * @param QueryCodeDto $dto
     * @param int $length
     *
     * @return void
     */
    private function maskStartOfLine(QueryCodeDto $dto, int $length): void
    {
        $spaces = str_repeat(' ', $length);
        $dto->inputLine = substr_replace($dto->inputLine, $spaces, 0, $length);
    }

    /**
     * @param QueryCodeDto $dto
     * @param int $offset
     *
     * @return void
     */
    private function maskEndOfLine(QueryCodeDto $dto, int $offset): void
    {
        $length = strlen($dto->inputLine) - $offset;
        $spaces = str_repeat(' ', $length);
        $dto->inputLine = substr_replace($dto->inputLine, $spaces, $offset, $length);
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
            $this->bufferLineContent($dto);
            return false;
        }

        // Last line of a multiline string.
        $this->maskStartOfLine($dto, $matches[1][1] + strlen($matches[1][0]));

        // Switch the string mode.
        $dto->inMultilineString = false;

        return true;
    }

    /**
     * @param QueryCodeDto $dto
     *
     * @return bool
     */
    private function parseMultiLineFunctionEnd(QueryCodeDto $dto): bool
    {
        if (!$dto->inMultilineFunction) {
            return true;
        }

        // Find the end of function delimiter.
        $regex = "/$dto->functionDelimiter/si";
        $flags = PREG_OFFSET_CAPTURE;
        $found = preg_match($regex, $dto->inputLine, $matches, $flags);
        if (!$found) {
            // Middle of a multiline function. Add the line to the buffer.
            $this->bufferLineContent($dto);
            return false;
        }

        // Last line of a multiline function.
        $this->maskStartOfLine($dto, $matches[1][1] + strlen($matches[1][0]));

        // Switch the function mode.
        $dto->inMultilineFunction = false;
        $dto->functionDelimiter = '';

        return true;
    }

    /**
     * @param QueryCodeDto $dto
     *
     * @return void
     */
    private function parseEndOfLine(QueryCodeDto $dto): void
    {
        // Find the start of comment or multiline string.
        $regex = "/('|--|\/\*|#|BEGIN|{$this->functionDelimiterRegex})/si";
        $flags = PREG_OFFSET_CAPTURE;
        $found = preg_match($regex, $dto->inputLine, $matches, $flags);
        // Nothing found.
        if (!$found) {
            return;
        }

        $delimiter = $matches[0][0];
        $offset = $matches[0][1];

        // Mask the end of the line.
        $this->maskEndOfLine($dto, $offset);

        // Start of multiline string found.
        if ($delimiter === "'") {
            // Switch the string mode.
            $dto->inMultilineString = true;
            return;
        }

        // Start of multiline comment found.
        if ($delimiter === '/*') {
            // Switch the comment mode.
            $dto->inMultilineComment = true;
            return;
        }

        // Single line comment found.
        if ($delimiter === '--' || $delimiter === '#') {
            return;
        }

        // Start of multiline function found
        // Switch the comment mode.
        $dto->inMultilineFunction = true;
        // "END" must always be followed by a delimiter. No "END IF", for example.
        $dto->functionDelimiter = strtoupper($delimiter) === 'BEGIN' ?
            "(END)\\s*{$dto->queryDelimiter}" : '(' . preg_quote($delimiter) . ')';
    }

    /**
     * @param QueryCodeDto $dto
     * @param array $regexes
     *
     * @return void
     */
    private function maskTokens(QueryCodeDto $dto, array $regexes): void
    {
        $callback = function(array $matches): string {
            // Replace the matched string with same length spaces.
            return str_repeat(' ', strlen($matches[0]));
        };
        $regex = implode('|', $regexes);
        $dto->inputLine = preg_replace_callback("/$regex/s", $callback, $dto->inputLine);
        // foreach ($regexes as $regex) {
        //     $dto->inputLine = preg_replace_callback("/$regex/s", $callback, $dto->inputLine);
        // }
    }

    /**
     * @param QueryCodeDto $dto
     *
     * @return bool
     */
    private function setDelimiter(QueryCodeDto $dto): bool
    {
        // Delimiter queries are not sent to the server.
        // $copyRegex = "~^(\\s*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i";
        // if ($this->_engine()->pgsql() && preg_match($copyRegex, $query, $matches)) {
        //     $dto->queryDelimiters = ['' => "\n\\\\\\.\r?\n"];
        //     return true;
        // }
        $delimiterRegex = "~^\\s*+DELIMITER\\s+(\\S+)~i";
        if (preg_match($delimiterRegex, $dto->queryLine, $matches)) {
            $dto->queryDelimiter = $matches[1];
            return true;
        }

        return false;
    }

    /**
     * @param QueryCodeDto $dto
     *
     * @return bool
     */
    private function makeInputLine(QueryCodeDto $dto): bool
    {
        if (trim($dto->queryLine) === '' || $this->setDelimiter($dto)) {
            $dto->queryLine = '';
            return false;
        }

        $dto->inputLine = $dto->queryLine;

        // Mask double quotes and antislashed quotes.
        $this->maskTokens($dto, [
            "''",
            "\\\\\\\\",
            "\\\\'",
            "\\\\`",
            "\\\\\"",
        ]);

        if (!$this->parseMultiLineStringEnd($dto)) {
            return false;
        }

        // Mask quoted strings.
        $this->maskTokens($dto, [
            "'[^']*'",
            "`[^`]*`",
            "\"[^\"]*\"",
        ]);

        if (!$this->parseMultiLineFunctionEnd($dto)) {
            return false;
        }

        if (!$this->parseMultiLineCommentEnd($dto)) {
            return false;
        }

        // Mask comments in "/*  */".
        $this->maskTokens($dto, [
            "\/\*.*?\*\/",
        ]);

        // Mask function in "$x$  $x$".
        $this->maskTokens($dto, [
            "({$this->functionDelimiterRegex}).*?\\1",
        ]);

        $this->parseEndOfLine($dto);

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
        while (($dto->queryLineReader)($dto)) {
            if (!$this->makeInputLine($dto)) {
                continue;
            }

            while (($offset = $this->findEndOfQuery($dto)) !== null) {
                $this->bufferEndOfQuery($dto, $offset);

                // Return the query for processing.
                if (($query = $this->getBufferedQuery($dto)) !== '') {
                    $dto->queryCount++;
                    yield $query;
                }
            }

            // Add the remaining input line to the query buffer.
            $this->bufferLineContent($dto);
        }

        // Return the last query.
        if (($query = $this->getBufferedQuery($dto)) !== '') {
            $dto->queryCount++;
            yield $query;
        }
    }
}
