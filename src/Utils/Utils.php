<?php

namespace Lagdo\DbAdmin\Driver\Utils;

use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;

use function array_key_exists;
use function in_array;
use function ini_get;
use function intval;
use function is_string;
use function preg_match;
use function substr;
use function strtolower;

class Utils
{
    /**
     * @param TranslatorInterface $trans
     * @param Input $input
     * @param Str $str
     */
    public function __construct(public TranslatorInterface $trans,
        public Input $input, public Str $str)
    {}

    /**
     * @param string $string
     *
     * @return string
     */
    public function html(string $string): string
    {
        return $this->str->html($string);
    }

    /**
     * Get a possibly missing item from a possibly missing array.
     * This is better than $row[$key] ?? null because PHP will report error for undefined $row.
     *
     * @param array<mixed>|null $array
     * @param array-key $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function idx(array|null $array, int|string $key, mixed $default = null): mixed
    {
        return $array !== null && array_key_exists($key, $array) ? $array[$key] : $default;
    }

    /**
     * Get INI boolean value
     *
     * @param string $ini
     *
     * @return bool
     */
    public function iniBool(string $ini): bool
    {
        $value = ini_get($ini);
        // boolean values set by php_value are strings
        return preg_match('~^(on|true|yes)$~i', $value) || (int)$value;
    }

    /**
     * Get INI bytes value
     *
     * @param string
     *
     * @return int
     */
    public function iniBytes(string $ini): int
    {
        $value = ini_get($ini);
        $unit = strtolower(substr($value, -1)); // Get the last char
        $ival = intval(substr($value, 0, -1)); // Remove the last char

        return match($unit) {
            'g' => intval($ival * 1024 * 1024 * 1024),
            'm' => intval($ival * 1024 * 1024),
            'k' => intval($ival * 1024),
            default => intval($value),
        };
    }

    /**
     * Find unique identifier of a row
     *
     * @param array $row
     * @param array $indexes Result of indexes()
     *
     * @return array|null
     */
    public function uniqueIds(array $row, array $indexes): array|null
    {
        foreach ($indexes as $index) {
            if (preg_match('~PRIMARY|UNIQUE~', $index->type)) {
                $ids = [];
                foreach ($index->columns as $key) {
                    if (!isset($row[$key])) { // NULL is ambiguous
                        continue 2;
                    }
                    $ids[$key] = $row[$key];
                }
                return $ids;
            }
        }
        // Null if there is no unique identifier
        return null;
    }

    /**
     * Check if the string is e-mail address
     *
     * @param mixed $email
     *
     * @return bool
     */
    public function isMail($email): bool
    {
        if (!is_string($email)) {
            return false;
        }

        $atom = '[-a-z0-9!#$%&\'*+/=?^_`{|}~]'; // characters of local-name
        $domain = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])'; // one domain component
        $pattern = "$atom+(\\.$atom+)*@($domain?\\.)+$domain";
        return preg_match("(^$pattern(,\\s*$pattern)*\$)i", $email) > 0;
    }

    /**
     * Check if the string is URL address
     *
     * @param mixed $string
     *
     * @return bool
     */
    public function isUrl($string): bool
    {
        if (!is_string($string)) {
            return false;
        }

        $domain = '[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])'; // one domain component //! IDN
        //! restrict path, query and fragment characters
        $pattern = "~^(https?)://($domain?\\.)+$domain(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i";
        return preg_match($pattern, $string) > 0;
    }

    /**
     * Check if the field is a blob
     *
     * @param TableFieldEntity $field
     * @param array $userTypes
     *
     * @return bool
     */
    public function isBlob(TableFieldEntity $field, array $userTypes = []): bool
    {
        return preg_match('~blob|bytea|raw|file~', $field->type) &&
            !in_array($field->type, $userTypes);
    }
}
