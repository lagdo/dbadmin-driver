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
}
