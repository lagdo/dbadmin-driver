<?php

namespace Lagdo\DbAdmin\Support;

use Lagdo\DbAdmin\Support\Db\Admin;
use Lagdo\DbAdmin\Support\Db\Engine;
use Lagdo\DbAdmin\Support\Utils\Utils;

abstract class AbstractGrammar implements GrammarInterface
{
    /**
     * @param AbstractDriver $driver
     * @param Utils $utils
     */
    public function __construct(protected AbstractDriver $driver, protected Utils $utils)
    {}

    use Admin\Grammar\SyntaxTrait;
    use Engine\Grammar\SyntaxTrait;
    use Admin\Grammar\DatabaseTrait;
    use Engine\Grammar\DatabaseTrait;
    use Admin\Grammar\TableTrait;
    use Engine\Grammar\TableTrait;
    use Admin\Grammar\QueryTrait;
    use Engine\Grammar\QueryTrait;
}
