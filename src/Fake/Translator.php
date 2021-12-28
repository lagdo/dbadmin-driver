<?php

namespace Lagdo\DbAdmin\Driver\Fake;

use Lagdo\DbAdmin\Driver\TranslatorInterface;

/**
 * Fake Translator class for testing
 */
class Translator implements TranslatorInterface
{
    /**
     * @inheritDoc
     */
    public function lang(string $idf, $number = null): string
    {
        return $idf;
    }
}
