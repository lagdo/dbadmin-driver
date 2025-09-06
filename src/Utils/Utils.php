<?php

namespace Lagdo\DbAdmin\Driver\Utils;

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
}
