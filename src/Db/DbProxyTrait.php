<?php

namespace Lagdo\DbAdmin\Support\Db;

use Lagdo\DbAdmin\Support\AbstractDriver;
use Lagdo\DbAdmin\Support\AbstractGrammar;
use Lagdo\DbAdmin\Support\Utils\Utils;

trait DbProxyTrait
{
    /**
     * @return AbstractDriver
     */
    abstract protected function _driver(): AbstractDriver;

    /**
     * @return AbstractGrammar
     */
    abstract protected function _grammar(): AbstractGrammar;

    /**
     * @return Utils
     */
    abstract protected function _utils(): Utils;
}
