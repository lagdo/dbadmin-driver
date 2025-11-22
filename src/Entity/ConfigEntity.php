<?php

namespace Lagdo\DbAdmin\Driver\Entity;

use Lagdo\DbAdmin\Driver\Utils\TranslatorInterface;

use function array_keys;
use function array_merge;
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
    public $version = '0.20.0';

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
     * The constructor
     *
     * @param TranslatorInterface $trans
     * @param array $options
     */
    public function __construct(public TranslatorInterface $trans, public array $options)
    {}

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
     * @return array
     */
    public function options(): array
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function onActions()
    {
        return explode('|', $this->actions);
    }
}
