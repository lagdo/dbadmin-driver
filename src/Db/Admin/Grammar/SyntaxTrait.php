<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Grammar;

use Lagdo\DbAdmin\Support\Dto\QueryDto;

trait SyntaxTrait
{
    /**
     * @var SyntaxInterface
     */
    private SyntaxInterface $syntax;

    /**
     * @return SyntaxInterface
     */
    private function _s(): SyntaxInterface
    {
        return $this->syntax ??= new Syntax($this->driver, $this, $this->utils);
    }

    /**
     * Get escaped table name
     *
     * @param string $idf
     *
     * @return string
     */
    public function escapeTableName(string $idf): string
    {
        return $this->_s()->escapeTableName($idf);
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
        return $this->_s()->bracketEscape($idf, $back);
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
        return $this->_s()->escapeKey($key);
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
        return $this->_s()->removeDefiner($query);
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
        return $this->_s()->processLength($length);
    }

    /**
     * Parse a string containing SQL queries
     *
     * @param QueryDto $queryDto
     *
     * @return bool
     */
    public function parseQueries(QueryDto $queryDto): bool
    {
        return $this->_s()->parseQueries($queryDto);
    }
}
