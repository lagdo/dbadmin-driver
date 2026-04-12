<?php

namespace Lagdo\DbAdmin\Driver\Sql;

use Lagdo\DbAdmin\Driver\AbstractEngine;
use Lagdo\DbAdmin\Driver\AbstractStatement;
use Lagdo\DbAdmin\Driver\Utils\Utils;

trait DbProxyTrait
{
    /**
     * @return AbstractEngine
     */
    abstract protected function _engine(): AbstractEngine;

    /**
     * @return AbstractStatement
     */
    abstract protected function _statement(): AbstractStatement;

    /**
     * @return Utils
     */
    abstract protected function _utils(): Utils;
}
