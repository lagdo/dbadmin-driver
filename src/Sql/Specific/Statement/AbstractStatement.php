<?php

namespace Lagdo\DbAdmin\Driver\Sql\Specific\Statement;

use Lagdo\DbAdmin\Driver\Sql\AbstractDbProxy;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;

use function preg_match;

abstract class AbstractStatement extends AbstractDbProxy
{
    /**
     * Get default value clause
     *
     * @param ColumnDto $column
     *
     * @return string
     */
    protected function getDefaultValueClause(ColumnDto $column): string
    {
        return match(true) {
            $column->default === null => '',
            preg_match('~char|binary|text|enum|set~', $column->type) > 0,
            preg_match('~^(?![a-z])~i', $column->default) > 0 => ' DEFAULT ' .
                $this->_engine()->quote($column->default),
            default => " DEFAULT {$column->default}",
        };
    }
}
