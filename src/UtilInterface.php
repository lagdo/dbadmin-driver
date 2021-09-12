<?php

namespace Lagdo\DbAdmin\Driver;

interface UtilInterface
{
    /**
     * Get the request inputs
     *
     * @return InputInterface
     */
    public function input();

    /**
     * Get a translated string
     * The first parameter is mandatory. Optional parameters can follow.
     *
     * @param string
     *
     * @return string
     */
    public function lang($idf);

    /**
     * Escape for HTML
     * @param string
     * @return string
     */
    public function html($string);

    /**
     * Remove non-digits from a string
     * @param string
     * @return string
     */
    public function number($val);

    /**
     * Check if the string is in UTF-8
     * @param string
     * @return bool
     */
    public function isUtf8($val);

    /**
     * Get INI boolean value
     * @param string
     * @return bool
     */
    public function iniBool($ini);

    /**
     * Convert \n to <br>
     * @param string
     * @return string
     */
    public function convertEolToHtml($string);

    /**
     * Compute fields() from input edit data
     * @return array
     */
    public function getFieldsFromEdit();

    /**
     * Create SQL string from field
     * @param array basic field information
     * @param array information about field type
     * @return array array("field", "type", "NULL", "DEFAULT", "ON UPDATE", "COMMENT", "AUTO_INCREMENT")
     */
    public function processField($field, $typeField);
}
