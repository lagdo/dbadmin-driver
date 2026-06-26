<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Sql\Standard;
use Lagdo\DbAdmin\Driver\Sql\Specific;

interface StatementInterface extends Standard\Statement\StatementInterface,
    Specific\Statement\DatabaseInterface, Specific\Statement\SyntaxInterface,
    Specific\Statement\QueryInterface, Specific\Statement\TableInterface
{}
