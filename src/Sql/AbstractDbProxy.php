<?php

namespace Lagdo\DbAdmin\Driver\Sql;

use Lagdo\DbAdmin\Driver\AbstractEngine;
use Lagdo\DbAdmin\Driver\AbstractStatement;
use Lagdo\DbAdmin\Driver\Utils\Utils;

abstract class AbstractDbProxy
{
    /**
     * @param AbstractEngine $engine
     * @param AbstractStatement $statement
     * @param Utils $utils
     */
    public function __construct(private AbstractEngine $engine,
        private AbstractStatement $statement, private Utils $utils)
    {}

    /**
     * @return AbstractEngine
     */
    protected function _engine(): AbstractEngine
    {
        return $this->engine;
    }

    /**
     * @return AbstractStatement
     */
    protected function _statement(): AbstractStatement
    {
        return $this->statement;
    }

    /**
     * @return Utils
     */
    protected function _utils(): Utils
    {
        return $this->utils;
    }
}
