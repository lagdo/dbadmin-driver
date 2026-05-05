<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Sql\Standard;
use Lagdo\DbAdmin\Driver\Sql\Specific;

interface StatementInterface extends Standard\Statement\DatabaseInterface,
    Specific\Statement\DatabaseInterface, Standard\Statement\SyntaxInterface,
    Specific\Statement\SyntaxInterface, Standard\Statement\QueryInterface,
    Specific\Statement\QueryInterface, Specific\Statement\TableInterface
{}
