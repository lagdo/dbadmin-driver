<?php

namespace Lagdo\DbAdmin\Driver\Tests;

use Lagdo\DbAdmin\Driver\InputInterface;
use Lagdo\DbAdmin\Driver\TranslatorInterface;
use Lagdo\DbAdmin\Driver\UtilInterface;
use Lagdo\DbAdmin\Driver\UtilTrait;

class Util implements UtilInterface
{
    use UtilTrait;

    /**
     * The constructor
     *
     * @param TranslatorInterface $trans
     * @param InputInterface $input
     */
    public function __construct(TranslatorInterface $trans, InputInterface $input)
    {
        $this->trans = $trans;
        $this->input = $input;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return '';
    }
}
