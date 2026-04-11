<?php

namespace Lagdo\DbAdmin\Support\Db;

use Lagdo\DbAdmin\Support\AbstractDriver;
use Lagdo\DbAdmin\Support\AbstractGrammar;
use Lagdo\DbAdmin\Support\Utils\Utils;

abstract class AbstractDelegate
{
    /**
     * @param AbstractDriver $driver
     * @param AbstractGrammar $grammar
     * @param Utils $utils
     */
    public function __construct(protected readonly AbstractDriver $driver,
        protected readonly AbstractGrammar $grammar, protected readonly Utils $utils)
    {}
}
