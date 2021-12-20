<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;

interface UtilInterface
{
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
    public function input();

    /**
     * Escape for HTML
     *
     * @param string|null $string
     *
     * @return string
     */
    public function html($string);

    /**
     * Remove non-digits from a string
     *
     * @param string $value
     *
     * @return string
     */
    public function number(string $value);

    /**
     * Check if the string is in UTF-8
     *
     * @param string $value
     *
     * @return bool
     */
    public function isUtf8(string $value);

    /**
     * Get INI boolean value
     *
     * @param string $ini
     *
     * @return bool
     */
    public function iniBool(string $ini);

    /**
     * Convert \n to <br>
     *
     * @param string $string
     *
     * @return string
     */
    public function convertEolToHtml(string $string);

    /**
     * Compute fields() from input edit data
     *
     * @return array
     */
    public function getFieldsFromEdit();

    /**
     * Create SQL string from field
     *
     * @param TableFieldEntity $field Basic field information
     * @param TableFieldEntity $typeField Information about field type
     *
     * @return array
     */
    public function processField(TableFieldEntity $field, TableFieldEntity $typeField);
}
