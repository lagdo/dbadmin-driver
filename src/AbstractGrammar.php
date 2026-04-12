<?php

namespace Lagdo\DbAdmin\Support;

use Lagdo\DbAdmin\Support\Db\Admin;
use Lagdo\DbAdmin\Support\Db\Engine;
use Lagdo\DbAdmin\Support\Utils\Utils;

abstract class AbstractGrammar implements GrammarInterface
{
    use Admin\Grammar\SyntaxTrait;
    use Engine\Grammar\SyntaxTrait;
    use Admin\Grammar\DatabaseTrait;
    use Engine\Grammar\DatabaseTrait;
    use Admin\Grammar\TableTrait;
    use Engine\Grammar\TableTrait;
    use Admin\Grammar\QueryTrait;
    use Engine\Grammar\QueryTrait;

    /**
     * @param AbstractDriver $driver
     * @param Utils $utils
     */
    public function __construct(private AbstractDriver $driver, private Utils $utils)
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
        return $this;
    }

    /**
     * @return Utils
     */
    protected function _utils(): Utils
    {
        return $this->utils;
    }
}
