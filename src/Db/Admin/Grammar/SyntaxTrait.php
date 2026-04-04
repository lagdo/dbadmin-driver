<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Grammar;

use Lagdo\DbAdmin\Support\Dto\QueryDto;

trait SyntaxTrait
{
    /**
     * @return SyntaxInterface
     */
    abstract protected function _syntax(): SyntaxInterface;

    /**
     * @inheritDoc
     */
    public function escapeId(string $idf): string
    {
        return $this->_syntax()->escapeId($idf);
    }

    /**
     * @inheritDoc
     */
    public function unescapeId(string $idf): string
    {
        return $this->_syntax()->unescapeId($idf);
    }

    /**
     * @inheritDoc
     */
    public function escapeTableName(string $idf): string
    {
        return $this->_syntax()->escapeTableName($idf);
    }

    /**
     * @inheritDoc
     */
    public function bracketEscape(string $idf, bool $back = false): string
    {
        return $this->_syntax()->bracketEscape($idf, $back);
    }

    /**
     * @inheritDoc
     */
    public function escapeKey(string $key): string
    {
        return $this->_syntax()->escapeKey($key);
    }

    /**
     * @inheritDoc
     */
    public function removeDefiner(string $query): string
    {
        return $this->_syntax()->removeDefiner($query);
    }

    /**
     * @inheritDoc
     */
    public function processLength(string $length): string
    {
        return $this->_syntax()->processLength($length);
    }

    /**
     * @inheritDoc
     */
    public function parseQueries(QueryDto $queryDto): bool
    {
        return $this->_syntax()->parseQueries($queryDto);
    }
}
