<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Statement;

use Lagdo\DbAdmin\Driver\Sql\AbstractDbProxy;

use function preg_match;
use function str_replace;
use function substr;

abstract class AbstractSyntax extends AbstractDbProxy implements SyntaxInterface
{
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
    public function getAutoIncrementType(string $type): string
    {
        return $type;
    }

    /**
     * @inheritDoc
     */
    public function processAttr(array $process, string $key, string $val): string
    {
        return $this->_utils()->html($val);
    }
}
