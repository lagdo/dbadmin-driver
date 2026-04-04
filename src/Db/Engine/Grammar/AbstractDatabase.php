<?php

namespace Lagdo\DbAdmin\Support\Db\Engine\Grammar;

use Lagdo\DbAdmin\Support\DriverInterface;
use Lagdo\DbAdmin\Support\Db\Admin\Grammar\DatabaseInterface;
use Lagdo\DbAdmin\Support\GrammarInterface;
use Lagdo\DbAdmin\Support\Utils\Utils;

use function preg_match;

abstract class AbstractDatabase implements DatabaseInterface
{
    /**
     * @var bool
     */
    protected $setCharset = false;

    /**
     * @param DriverInterface $driver
     * @param GrammarInterface $grammar
     * @param Utils $utils
     */
    public function __construct(protected DriverInterface $driver,
        protected GrammarInterface $grammar, protected Utils $utils)
    {}

    /**
     * @inheritDoc
     */
    public function setUtf8mb4(string $create): void
    {
        // possible false positive
        if (!$this->setCharset && preg_match('~\butf8mb4~i', $create)) {
            $this->setCharset = true;
        }
    }

    /**
     * @inheritDoc
     */
    public function getCharsetQuery(): string
    {
        return !$this->setCharset ? '' : 'SET NAMES ' . $this->driver->charset() . ";\n\n";
    }
}
