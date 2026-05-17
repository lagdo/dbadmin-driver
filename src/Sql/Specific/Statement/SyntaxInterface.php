<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Statement;

interface SyntaxInterface
{
    /**
     * Escape database identifier
     *
     * @param string $idf
     *
     * @return string
     */
    public function escapeId(string $idf): string;

    /**
     * Unescape database identifier
     *
     * @param string $idf
     *
     * @return string
     */
    public function unescapeId(string $idf): string;

    /**
     * Get the real column type depending on the user input
     *
     * @param string $type
     *
     * @return string
     */
    public function getAutoIncrementType(string $type): string;

    /**
     * Format a process attribute
     *
     * @param array $process
     * @param string $key
     * @param string $val
     *
     * @return string
     */
    public function processAttr(array $process, string $key, string $val): string;
}
