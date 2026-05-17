<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Statement;

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
     * Get the real column type depending on the user input
     *
     * @param string $type
     *
     * @return string
     */
    public function getAutoIncrementType(string $type): string
    {
        return $this->_syntax()->getAutoIncrementType($type);
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
