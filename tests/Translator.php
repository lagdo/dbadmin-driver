<?php

namespace Lagdo\DbAdmin\Driver\Tests;

use Lagdo\DbAdmin\Driver\TranslatorInterface;

class Translator implements TranslatorInterface
{

    /**
     * @inheritDoc
     */
    public function lang(string $idf, $number = null)
    {
        // TODO: Implement lang() method.
    }

    /**
     * @inheritDoc
     */
    public function formatNumber(int $number)
    {
        // TODO: Implement formatNumber() method.
    }

    /**
     * @inheritDoc
     */
    public function formatTime(float $time)
    {
        // TODO: Implement formatTime() method.
    }
}