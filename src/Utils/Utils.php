<?php

namespace Lagdo\DbAdmin\Driver\Utils;

class Utils
{
    /**
     * @var TranslatorInterface
     */
    public $trans;

    /**
     * @var Input
     */
    public $input;

    /**
     * @var Str
     */
    public $str;

    /**
     * @var History
     */
    public $history;

    /**
     * @param TranslatorInterface $trans
     * @param Input $input
     * @param Str $str
     * @param History $history
     */
    public function __construct(TranslatorInterface $trans, Input $input, Str $str, History $history)
    {
        $this->trans = $trans;
        $this->input = $input;
        $this->str = $str;
        $this->history = $history;
    }

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
