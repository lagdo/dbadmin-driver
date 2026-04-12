<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Sql\Specific;
use Lagdo\DbAdmin\Driver\Sql\Specific\Config\DriverConfig;
use Lagdo\DbAdmin\Driver\Sql\Universal;
use Lagdo\DbAdmin\Driver\Utils\Utils;

abstract class AbstractEngine implements EngineInterface
{
    use Specific\Config\ConfigTrait;
    use Specific\Connection\ConnectionTrait;
    use Universal\Engine\ServerTrait;
    use Specific\Engine\ServerTrait;
    use Universal\Engine\DatabaseTrait;
    use Specific\Engine\DatabaseTrait;
    use Universal\Engine\TableTrait;
    use Specific\Engine\TableTrait;
    use Universal\Engine\QueryTrait;
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
        $this->_server()->initConnection($this->config);
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
