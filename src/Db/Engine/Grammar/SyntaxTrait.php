<?php

namespace Lagdo\DbAdmin\Support\Db\Engine\Grammar;

trait SyntaxTrait
{
    /**
     * @return AbstractSyntax
     */
    abstract protected function _syntax(): AbstractSyntax;

    /**
     * Escape database identifier
     *
     * @param string $idf
     *
     * @return string
     */
    public function escapeId(string $idf): string
    {
        return $this->_syntax()->escapeId($idf);
    }

    /**
     * Unescape database identifier
     *
     * @param string $idf
     *
     * @return string
     */
    public function unescapeId(string $idf): string
    {
        return $this->_syntax()->unescapeId($idf);
    }

    /**
     * Get a process name
     *
     * @param array $process
     * @param string $key
     * @param string $val
     *
     * @return string
     */
    public function processAttr(array $process, string $key, string $val): string
    {
        return $this->_syntax()->processAttr($process, $key, $val);
    }
}
