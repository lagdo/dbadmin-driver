<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Sql\Config\ConfigTrait;
use Lagdo\DbAdmin\Driver\Sql\Config\DriverConfig;
use Lagdo\DbAdmin\Driver\Sql\Connection\ConnectionTrait;
use Lagdo\DbAdmin\Driver\Sql\Specific;
use Lagdo\DbAdmin\Driver\Sql\Standard;
use Lagdo\DbAdmin\Driver\Utils\Utils;

abstract class AbstractEngine implements EngineInterface
{
    use ConfigTrait;
    use ConnectionTrait;
    use Standard\Engine\ServerTrait;
    use Specific\Engine\ServerTrait;
    use Standard\Engine\DatabaseTrait;
    use Specific\Engine\DatabaseTrait;
    use Standard\Engine\TableTrait;
    use Specific\Engine\TableTrait;
    use Standard\Engine\QueryTrait;
    use Specific\Engine\QueryTrait;

    /**
     * @var AbstractStatement
     */
    private AbstractStatement $statement;

    /**
     * @param Utils $utils
     * @param array $options
     */
    public function __construct(private Utils $utils, array $options)
    {
        $this->config = new DriverConfig($utils->trans, $options);
    }

    /**
     * @param AbstractStatement $statement
     *
     * @return void
     */
    public function setStatement(AbstractStatement $statement): void
    {
        $this->statement = $statement;
        // Must be called after the statement is set.
        $this->_server()->setConfig($this->config);
    }

    /**
     * @return Utils
     */
    protected function _utils(): Utils
    {
        return $this->utils;
    }

    /**
     * @return AbstractEngine
     */
    protected function _engine(): AbstractEngine
    {
        return $this;
    }

    /**
     * @return AbstractStatement
     */
    protected function _statement(): AbstractStatement
    {
        return $this->statement;
    }
}
