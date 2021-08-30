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
    public function h($string);

    /**
     * Remove non-digits from a string
     * @param string
     * @return string
     */
    public function number($val);

    /**
     * Check whether the string is in UTF-8
     * @param string
     * @return bool
     */
    public function is_utf8($val);

    /**
     * Get INI boolean value
     * @param string
     * @return bool
     */
    public function ini_bool($ini);

    /**
     * Convert \n to <br>
     * @param string
     * @return string
     */
    public function nl_br($string);

    /**
     * Compute fields() from input edit data
     * @return array
     */
    public function fields_from_edit();

    /**
     * Create SQL string from field
     * @param array basic field information
     * @param array information about field type
     * @return array array("field", "type", "NULL", "DEFAULT", "ON UPDATE", "COMMENT", "AUTO_INCREMENT")
     */
    public function process_field($field, $type_field);
}
