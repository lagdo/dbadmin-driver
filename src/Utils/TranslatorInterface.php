<?php

namespace Lagdo\DbAdmin\Support\Utils;

interface TranslatorInterface
{
    /**
     * Get a translated string
     * The first parameter is mandatory. Optional parameters can follow.
     *
     * @param string $idf
     * @param mixed $number
     *
     * @return string
     */
    public function lang(string $idf, $number = null): string;
}
