<?php

namespace Lagdo\DbAdmin\Support\Db;

use Lagdo\DbAdmin\Support\AbstractDriver;
use Lagdo\DbAdmin\Support\AbstractGrammar;
use Lagdo\DbAdmin\Support\Utils\Utils;

abstract class AbstractDbProxy
{
    /**
     * @param AbstractDriver $driver
     * @param AbstractGrammar $grammar
     * @param Utils $utils
     */
    public function __construct(private AbstractDriver $driver,
        private AbstractGrammar $grammar, private Utils $utils)
    {}

    /**
     * @return AbstractDriver
     */
    protected function _driver(): AbstractDriver
    {
        return $this->driver;
    }

    /**
     * @return AbstractGrammar
     */
    protected function _grammar(): AbstractGrammar
    {
        return $this->grammar;
    }

    /**
     * @return Utils
     */
    protected function _utils(): Utils
    {
        return $this->utils;
    }
}
