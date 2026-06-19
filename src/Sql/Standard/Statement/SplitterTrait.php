<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Statement;

use Lagdo\DbAdmin\Driver\Sql\DbProxyTrait;
use Lagdo\DbAdmin\Driver\Sql\Dto\QueryStreamDto;
use Generator;

use function array_filter;
use function implode;
use function preg_match;
use function preg_quote;
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
     * @var string
     */
    private string $delimiterQueryRegex = "~^\\s*+DELIMITER\\s+(\\S+)~i";

    /**
     * @param QueryStreamDto $stream
     *
     * @return string
     */
    private function getBufferedQuery(QueryStreamDto $stream): string
    {
        $query = trim(implode('', $stream->queryBuffer));
        $stream->queryBuffer = [];

        return $query;
    }

    /**
     * @param QueryStreamDto $stream
     *
     * @return int|null
     */
    private function findEndOfQuery(QueryStreamDto $stream): int|null
    {
        $offset = strpos($stream->inputLine, $stream->queryDelimiter);
        return $offset === false ? null : $offset;
    }

    /**
     * Return the delimiter position, or null.
     *
     * @param QueryStreamDto $stream
     * @param string $delimiter
     * @param bool $withLength
     *
     * @return int|null
     */
    private function findDelimiterPosition(QueryStreamDto $stream,
        string $delimiter, bool $withLength): int|null
    {
        $regex = "/^{$this->queryRegex}*[^'\"`]*\$/s";
        $offset = 0;
        $delimiterLength = strlen($delimiter);

        // Todo: can this be done with a single regex?
        while (($offset = strpos($stream->inputLine, $delimiter, $offset)) !== false) {
            // Take only the delimiters not enclosed into quotes or double quotes.
            if (preg_match($regex, substr($stream->inputLine, 0, $offset), $matches)) {
                return $withLength ? $offset + $delimiterLength : $offset;
            }

            $offset += $delimiterLength;
        }

        return null;
    }

    /**
     * @param QueryStreamDto $stream
     *
     * @return bool
     */
    private function parseMultiLineCommentEnd(QueryStreamDto $stream): bool
    {
        if (!$stream->inMultilineComment) {
            return true;
        }

        // Find the end of comment delimiter.
        $offset = $this->findDelimiterPosition($stream, '*/', true);
        if ($offset === null) {
            // Middle of a multiline comment. Skip the line.
            return false;
        }

        // Last line of a multiline comment. Truncate the start.
        $this->truncateStartOfLine($stream, $offset);

        // Switch the comment mode.
        $stream->inMultilineComment = false;

        return true;
    }

    /**
     * @param QueryStreamDto $stream
     *
     * @return void
     */
    private function bufferLineContent(QueryStreamDto $stream): void
    {
        if ($stream->queryLine !== '') {
            $stream->queryBuffer[] = $stream->queryLine;
            $stream->queryLine = '';
        }
    }

    /**
     * @param QueryStreamDto $stream
     * @param int $offset
     *
     * @return void
     */
    private function bufferEndOfQuery(QueryStreamDto $stream, int $offset): void
    {
        // Copy the start of the line to the buffer.
        $stream->queryBuffer[] = substr($stream->queryLine, 0, $offset);
        // Truncate the start of the line.
        $offset += strlen($stream->queryDelimiter);
        $stream->queryLine = substr($stream->queryLine, $offset);
        $stream->inputLine = substr($stream->inputLine, $offset);
    }

    /**
     * @param QueryStreamDto $stream
     * @param int $offset
     *
     * @return void
     */
    private function truncateStartOfLine(QueryStreamDto $stream, int $offset): void
    {
        $stream->queryLine = substr($stream->queryLine, $offset);
        $stream->inputLine = substr($stream->inputLine, $offset);
    }

    /**
     * @param QueryStreamDto $stream
     * @param int $offset
     *
     * @return void
     */
    private function truncateEndOfLine(QueryStreamDto $stream, int $offset): void
    {
        $stream->queryLine = substr($stream->queryLine, 0, $offset);
        $stream->inputLine = substr($stream->inputLine, 0, $offset);
    }

    /**
     * @param QueryStreamDto $stream
     * @param int $length
     *
     * @return void
     */
    private function maskStartOfLine(QueryStreamDto $stream, int $length): void
    {
        $spaces = str_repeat(' ', $length);
        $stream->inputLine = substr_replace($stream->inputLine, $spaces, 0, $length);
    }

    /**
     * @param QueryStreamDto $stream
     * @param int $offset
     *
     * @return void
     */
    private function maskEndOfLine(QueryStreamDto $stream, int $offset): void
    {
        $length = strlen($stream->inputLine) - $offset;
        $spaces = str_repeat(' ', $length);
        $stream->inputLine = substr_replace($stream->inputLine, $spaces, $offset, $length);
    }

    /**
     * @param QueryStreamDto $stream
     *
     * @return bool
     */
    private function parseMultiLineStringEnd(QueryStreamDto $stream): bool
    {
        if (!$stream->inMultilineString) {
            return true;
        }

        $regex = "/('\s*)/s";
        $flags = PREG_OFFSET_CAPTURE;
        $found = preg_match($regex, $stream->inputLine, $matches, $flags);
        if (!$found) {
            // Middle of a multiline string. Add the line to the buffer.
            $this->bufferLineContent($stream);
            return false;
        }

        // Last line of a multiline string.
        $this->maskStartOfLine($stream, $matches[1][1] + strlen($matches[1][0]));

        // Switch the string mode.
        $stream->inMultilineString = false;

        return true;
    }

    /**
     * @param QueryStreamDto $stream
     *
     * @return bool
     */
    private function parseMultiLineFunctionEnd(QueryStreamDto $stream): bool
    {
        if (!$stream->inMultilineFunction) {
            return true;
        }

        // Find the end of function delimiter.
        $regex = "/$stream->functionDelimiterRegex/si";
        $flags = PREG_OFFSET_CAPTURE;
        $found = preg_match($regex, $stream->inputLine, $matches, $flags);
        if (!$found) {
            // Middle of a multiline function. Add the line to the buffer.
            $this->bufferLineContent($stream);
            return false;
        }

        // Last line of a multiline function.
        $this->maskStartOfLine($stream, $matches[1][1] + strlen($matches[1][0]));

        // Switch the function mode.
        $stream->inMultilineFunction = false;
        $stream->functionDelimiterRegex = '';

        return true;
    }

    /**
     * @param QueryStreamDto $stream
     *
     * @return void
     */
    private function parseTokensAfterDelimiter(QueryStreamDto $stream): void
    {
        // Find the start of comment or multiline string.
        $regex = "/('|--|\/\*|#|BEGIN|{$this->functionDelimiterRegex})/si";
        $flags = PREG_OFFSET_CAPTURE;
        $found = preg_match($regex, $stream->inputLine, $matches, $flags);
        // Nothing found.
        if (!$found) {
            return;
        }

        $delimiter = $matches[0][0];
        $offset = $matches[0][1];

        // Start of multiline string found.
        if ($delimiter === "'") {
            // Mask the end of the line.
            $this->maskEndOfLine($stream, $offset);
            // Switch the string mode.
            $stream->inMultilineString = true;
            return;
        }

        // Start of multiline comment found.
        if ($delimiter === '/*') {
            // Truncate the end of the line.
            $this->truncateEndOfLine($stream, $offset);
            // Switch the comment mode.
            $stream->inMultilineComment = true;
            return;
        }

        // Single line comment found.
        if ($delimiter === '--' || $delimiter === '#') {
            // Truncate the end of the line.
            $this->truncateEndOfLine($stream, $offset);
            return;
        }

        // Start of multiline function found
        // Mask the end of the line.
        $this->maskEndOfLine($stream, $offset);
        // Switch the comment mode.
        $stream->inMultilineFunction = true;
        // "END" must always be followed by a delimiter. No "END IF", for example.
        $stream->functionDelimiterRegex = strtoupper($delimiter) === 'BEGIN' ?
            "(END)\\s*{$stream->pregQueryDelimiter}" : '(' . preg_quote($delimiter) . ')';
    }

    /**
     * @param QueryStreamDto $stream
     * @param array $regexes
     *
     * @return void
     */
    private function maskTokens(QueryStreamDto $stream, array $regexes): void
    {
        // Make sure the delimiter is not masked.
        $regexes = array_filter($regexes, fn($regex) => $regex !== $stream->pregQueryDelimiter);

        $callback = fn(array $matches) => str_repeat(' ', strlen($matches[0]));
        $regex = implode('|', $regexes);
        $stream->inputLine = preg_replace_callback("/$regex/s", $callback, $stream->inputLine);
        // foreach ($regexes as $regex) {
        //     $stream->inputLine = preg_replace_callback("/$regex/s", $callback, $stream->inputLine);
        // }
    }

    /**
     * @param QueryStreamDto $stream
     *
     * @return bool
     */
    private function setDelimiter(QueryStreamDto $stream): bool
    {
        // Delimiter queries are not sent to the server.
        // $copyRegex = "~^(\\s*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i";
        // if ($this->_engine()->pgsql() && preg_match($copyRegex, $query, $matches)) {
        //     $stream->queryDelimiters = ['' => "\n\\\\\\.\r?\n"];
        //     return true;
        // }
        if (preg_match($this->delimiterQueryRegex, $stream->queryLine, $matches)) {
            $stream->queryDelimiter = $matches[1];
            $stream->pregQueryDelimiter = preg_quote($stream->queryDelimiter);
            return true;
        }

        return false;
    }

    /**
     * @param QueryStreamDto $stream
     *
     * @return bool
     */
    private function makeInputLine(QueryStreamDto $stream): bool
    {
        if ($this->setDelimiter($stream)) {
            $stream->queryLine = '';
            return false;
        }

        $stream->inputLine = $stream->queryLine;

        // Mask double quotes and antislashed quotes.
        $this->maskTokens($stream, [
            "''",
            "\\\\\\\\",
            "\\\\'",
            "\\\\`",
            "\\\\\"",
        ]);

        if (!$this->parseMultiLineStringEnd($stream)) {
            return false;
        }

        // Mask quoted strings.
        $this->maskTokens($stream, [
            "'[^']*'",
            "`[^`]*`",
            "\"[^\"]*\"",
        ]);

        if (!$this->parseMultiLineFunctionEnd($stream)) {
            return false;
        }

        if (!$this->parseMultiLineCommentEnd($stream)) {
            return false;
        }

        // Mask comments in "/*  */".
        $this->maskTokens($stream, [
            "\/\*.*?\*\/",
        ]);

        // Mask function in "$x$  $x$".
        $this->maskTokens($stream, [
            "({$this->functionDelimiterRegex}).*?\\1",
        ]);

        $this->parseTokensAfterDelimiter($stream);

        if (trim($stream->queryLine) === '') {
            $stream->queryLine = '';
            return false;
        }

        return true;
    }

    /**
     * Split a string or a file containing SQL queries.
     *
     * @param QueryStreamDto $stream
     *
     * @return Generator
     */
    public function splitQueries(QueryStreamDto $stream): Generator
    {
        while (($stream->queryLineReader)($stream)) {
            if (!$this->makeInputLine($stream)) {
                continue;
            }

            while (($offset = $this->findEndOfQuery($stream)) !== null) {
                $this->bufferEndOfQuery($stream, $offset);

                // Return the query for processing.
                if (($query = $this->getBufferedQuery($stream)) !== '') {
                    $stream->queryCount++;
                    yield $query;
                }
            }

            if (trim($stream->queryLine) !== '') {
                // Add the remaining input line to the query buffer.
                $this->bufferLineContent($stream);
            }
        }

        // Return the last query.
        if (($query = $this->getBufferedQuery($stream)) !== '') {
            $stream->queryCount++;
            yield $query;
        }
    }
}
