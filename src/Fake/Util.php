<?php

namespace Lagdo\DbAdmin\Driver\Fake;

use Lagdo\DbAdmin\Driver\Input;
use Lagdo\DbAdmin\Driver\TranslatorInterface;
use Lagdo\DbAdmin\Driver\UtilInterface;
use Lagdo\DbAdmin\Driver\UtilTrait;

/**
 * Fake Util class for testing
 */
class Util implements UtilInterface
{
    use UtilTrait;

    /**
     * The constructor
     *
     * @param TranslatorInterface $trans
     * @param Input $input
     */
    public function __construct(TranslatorInterface $trans, Input $input)
    {
        $this->trans = $trans;
        $this->input = $input;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return 'Test driver';
    }
}
