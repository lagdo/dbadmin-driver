<?php

namespace Lagdo\DbAdmin\Driver;

interface UtilInterface
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
     * @return InputInterface
     */
    public function input(): InputInterface;

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
}
