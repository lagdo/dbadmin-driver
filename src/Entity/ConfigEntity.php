<?php

namespace Lagdo\DbAdmin\Driver\Entity;

use Lagdo\DbAdmin\Driver\Utils\TranslatorInterface;

use function trim;
use function array_keys;
use function array_merge;
use function array_key_exists;
use function explode;

class ConfigEntity
{
    /**
     * @var string
     */
    public $jush = '';

    /**
     * @var string
     */
    public $version = '4.8.1-dev';

    /**
     * @var array
     */
    public $drivers = [];

    /**
     * @var array
     */
    public $types = [];

    /**
     * @var array
     */
    public $features = [];

    /**
     * @var array
     */
    public $structuredTypes = [];

    /**
     * @var array
     */
    public $unsigned = [];

    /**
     * @var array
     */
    public $operators = [];

    /**
     * @var array
     */
    public $functions = [];

    /**
     * @var array
     */
    public $grouping = [];

    /**
     * @var array
     */
    public $editFunctions = [];

    /**
     * @var array
     */
    public $options;

    /**
     * From bootstrap.inc.php
     * /// used in foreignKeys()
     * @var string
     */
    public $actions = 'RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT';

    /**
     * // not point, not interval
     * @var string
     */
    public $numberRegex = '((?<!o)int(?!er)|numeric|real|float|double|decimal|money)';

    /**
     * From index.php
     * @var string
     */
    public $inout = 'IN|OUT|INOUT';

    /**
     * From index.php
     * @var string
     */
    public $enumLength = "'(?:''|[^'\\\\]|\\\\.)*'";

    /**
     * The current database name
     *
     * @var string
     */
    public $database = '';

    /**
     * The current schema name
     *
     * @var string
     */
    public $schema = '';

    /**
     * @var TranslatorInterface
     */
    public $trans;

    /**
     * The constructor
     *
     * @param TranslatorInterface $trans
     * @param array $options
     */
    public function __construct(TranslatorInterface $trans, array $options)
    {
        $this->trans = $trans;
        $this->options = $options;
    }

    /**
     * Set the supported types
     *
     * @param array $types
     *
     * @return void
     */
    public function setTypes(array $types)
    {
        foreach ($types as $group => $typeGroup) {
            $this->structuredTypes[$this->trans->lang($group)] = array_keys($typeGroup);
            $this->types = array_merge($this->types, $typeGroup);
        }
    }

    /**
     * Get the driver options
     *
     * @param string $name The option name
     * @param mixed $default
     *
     * @return mixed
     */
    public function options(string $name = '', $default = '')
    {
        if (!($name = trim($name))) {
            return $this->options;
        }
        if (array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }
        if ($name === 'server') {
            $server = $this->options['host'] ?? '';
            $port = $this->options['port'] ?? ''; // Optional
            // Append the port to the host if it is defined.
            if (($port)) {
                $server .= ":$port";
            }
            return $server;
        }
        // if ($name === 'ssl') {
        //     return false; // No SSL options yet
        // }
        // Option not found
        return $default;
    }

    /**
     * @return array
     */
    public function onActions()
    {
        return explode('|', $this->actions);
    }
}
