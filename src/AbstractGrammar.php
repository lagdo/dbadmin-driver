<?php

namespace Lagdo\DbAdmin\Support;

abstract class AbstractGrammar implements GrammarInterface
{
    use Db\Admin\Grammar\SyntaxTrait;
    use Db\Admin\Grammar\DatabaseTrait;
    use Db\Admin\Grammar\TableTrait;
    use Db\Admin\Grammar\QueryTrait;
}
