<?php

namespace Lagdo\DbAdmin\Support;

use Lagdo\DbAdmin\Support\Db\Admin;
use Lagdo\DbAdmin\Support\Db\Engine;

interface GrammarInterface extends Admin\Grammar\DatabaseInterface,
    Engine\Grammar\DatabaseInterface, Admin\Grammar\SyntaxInterface,
    Engine\Grammar\SyntaxInterface, Admin\Grammar\TableInterface,
    Engine\Grammar\TableInterface, Admin\Grammar\QueryInterface,
    Engine\Grammar\QueryInterface
{}
