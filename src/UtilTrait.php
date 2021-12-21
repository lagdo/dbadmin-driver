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
     * @var InputInterface
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
    public function input(): InputInterface
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
}
