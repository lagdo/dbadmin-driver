<?php

namespace Lagdo\DbAdmin\Support\Db\Fake;

use Lagdo\DbAdmin\Support\Utils\TranslatorInterface;

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
