<?php

namespace Lagdo\DbAdmin\Driver;

use function str_replace;
use function preg_replace;
use function preg_match;
use function htmlspecialchars;

trait UtilTrait
{
    /**
     * @var DriverInterface
     */
    public $driver;

    /**
     * @var TranslatorInterface
     */
    protected $trans;

    /**
     * @var Input
     */
    public $input;

    /**
     * Set the driver
     *
     * @param DriverInterface $driver
     *
     * @return void
     */
    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    /**
     * @inheritDoc
     */
    public function input(): Input
    {
        return $this->input;
    }

    /**
     * @inheritDoc
     */
    public function html($string): string
    {
        if(!$string) {
            return '';
        }
        $string =  str_replace("\n", '<br>', $string);
        return str_replace("\0", '&#0;', htmlspecialchars($string, ENT_QUOTES, 'utf-8'));
    }

    /**
     * @inheritDoc
     */
    public function number(string $value): string
    {
        return preg_replace('~[^0-9]+~', '', $value);
    }

    /**
     * @inheritDoc
     */
    public function isUtf8(string $value): bool
    {
        // don't print control chars except \t\r\n
        return (preg_match('~~u', $value) && !preg_match('~[\0-\x8\xB\xC\xE-\x1F]~', $value));
    }

    /**
     * Create repeat pattern for preg
     *
     * @param string $pattern
     * @param int $length
     *
     * @return string
     */
    public function repeatPattern(string $pattern, int $length): string
    {
        // fix for Compilation failed: number too big in {} quantifier
        // can create {0,0} which is OK
        return str_repeat("$pattern{0,65535}", $length / 65535) . "$pattern{0," . ($length % 65535) . '}';
    }

    /**
     * Shorten UTF-8 string
     *
     * @param string $string
     * @param int $length
     * @param string $suffix
     *
     * @return string
     */
    public function shortenUtf8(string $string, int $length = 80, string $suffix = ''): string
    {
        if (!preg_match('(^(' . $this->repeatPattern("[\t\r\n -\x{10FFFF}]", $length) . ')($)?)u', $string, $match)) {
            // ~s causes trash in $match[2] under some PHP versions, (.|\n) is slow
            preg_match('(^(' . $this->repeatPattern("[\t\r\n -~]", $length) . ')($)?)', $string, $match);
        }
        return $this->html($match[1]) . $suffix . (isset($match[2]) ? '' : '<i>…</i>');
    }
}
