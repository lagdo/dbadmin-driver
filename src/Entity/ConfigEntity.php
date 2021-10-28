<?php

namespace Lagdo\DbAdmin\Driver\Entity;

use Lagdo\DbAdmin\Driver\TranslatorInterface;

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
     * @var string
     */
    public $onActions = 'RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT'; ///< @var string used in foreignKeys()

    /**
     * @var string
     */
    public $numberRegex = '((?<!o)int(?!er)|numeric|real|float|double|decimal|money)'; // not point, not interval

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
     * Set the supported types
     *
     * @param array $types
     * @param TranslatorInterface $trans
     *
     * @return void
     */
    public function setTypes(array $types, TranslatorInterface $trans)
    {
        foreach ($types as $group => $_types) {
            $this->structuredTypes[$trans->lang($group)] = array_keys($_types);
            $this->types = array_merge($this->types, $_types);
        }
    }

    /**
     * The constructor
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * Get the driver options
     *
     * @param string $name The option name
     *
     * @return mixed
     */
    public function options(string $name = '')
    {
        if (!($name = trim($name))) {
            return $this->options;
        }
        if (\array_key_exists($name, $this->options)) {
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
        return '';
    }

    /**
     * @return array
     */
    public function onActions()
    {
        return \explode('|', $this->onActions);
    }
}
