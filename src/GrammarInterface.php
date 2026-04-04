<?php

namespace Lagdo\DbAdmin\Support;

use Lagdo\DbAdmin\Support\Db\Admin\Grammar;

interface GrammarInterface extends Grammar\DatabaseInterface,
    Grammar\SyntaxInterface, Grammar\TableInterface, Grammar\QueryInterface
{}
