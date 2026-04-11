<?php

namespace Lagdo\DbAdmin\Support\Db\Engine\Grammar;

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
