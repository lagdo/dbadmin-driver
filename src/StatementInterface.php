<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Sql\Universal;
use Lagdo\DbAdmin\Driver\Sql\Specific;

interface StatementInterface extends Universal\Statement\DatabaseInterface,
    Specific\Statement\DatabaseInterface, Universal\Statement\SyntaxInterface,
    Specific\Statement\SyntaxInterface, Universal\Statement\TableInterface,
    Specific\Statement\TableInterface, Universal\Statement\QueryInterface,
    Specific\Statement\QueryInterface
{}
