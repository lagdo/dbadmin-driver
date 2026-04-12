<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Sql\Specific;
use Lagdo\DbAdmin\Driver\Sql\Universal;
use Lagdo\DbAdmin\Driver\Utils\Utils;

abstract class AbstractStatement implements StatementInterface
{
    use Universal\Statement\SyntaxTrait;
    use Specific\Statement\SyntaxTrait;
    use Universal\Statement\DatabaseTrait;
    use Specific\Statement\DatabaseTrait;
    use Universal\Statement\TableTrait;
    use Specific\Statement\TableTrait;
    use Universal\Statement\QueryTrait;
    use Specific\Statement\QueryTrait;

    /**
     * @var AbstractEngine
     */
    private AbstractEngine $engine;

    /**
     * @param Utils $utils
     */
    public function __construct(private Utils $utils)
    {}

    /**
     * @param AbstractEngine $engine
     *
     * @return void
     */
    public function setEngine(AbstractEngine $engine): void
    {
        $this->engine = $engine;
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
        return $this->engine;
    }

    /**
     * @return AbstractStatement
     */
    protected function _statement(): AbstractStatement
    {
        return $this;
    }
}
