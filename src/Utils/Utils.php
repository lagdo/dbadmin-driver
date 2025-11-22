<?php

namespace Lagdo\DbAdmin\Driver\Utils;

use function array_key_exists;

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
}
