<?php

namespace Lagdo\DbAdmin\Driver;

interface TranslatorInterface
{
    /**
     * Get a translated string
     * The first parameter is mandatory. Optional parameters can follow.
     *
     * @param string $idf
     * @param int $number
     *
     * @return string
     */
    public function lang(string $idf, $number = null);

    /**
     * Format a decimal number
     *
     * @param int $number
     *
     * @return string
     */
    public function formatNumber(int $number);

    /**
     * Format elapsed time
     *
     * @param float $time Output of microtime(true)
     *
     * @return string
     */
    public function formatTime(float $time);
}
