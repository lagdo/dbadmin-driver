<?php

namespace Lagdo\DbAdmin\Driver;

interface AdminInterface
{
    /**
     * Name in title and navigation
     *
     * @return string
     */
    public function name(): string;

    /**
     * Set the driver
     *
     * @param DriverInterface $driver
     *
     * @return void
     */
    public function setDriver(DriverInterface $driver);

    /**
     * Get the request inputs
     *
     * @return Input
     */
    public function input(): Input;

    /**
     * Escape for HTML
     *
     * @param string|null $string
     *
     * @return string
     */
    public function html($string): string;

    /**
     * Remove non-digits from a string
     *
     * @param string $value
     *
     * @return string
     */
    public function number(string $value): string;

    /**
     * Check if the string is in UTF-8
     *
     * @param string $value
     *
     * @return bool
     */
    public function isUtf8(string $value): bool;

    /**
     * Shorten UTF-8 string
     *
     * @param string $string
     * @param int $length
     * @param string $suffix
     *
     * @return string
     */
    public function shortenUtf8(string $string, int $length = 80, string $suffix = ''): string;

    /**
     * Escape or unescape string to use inside form []
     *
     * @param string $idf
     * @param bool $back
     *
     * @return string
     */
    public function bracketEscape(string $idf, bool $back = false): string;

    /**
     * Escape column key used in where()
     *
     * @param string
     *
     * @return string
     */
    public function escapeKey(string $key): string;
}
