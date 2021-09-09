<?php

namespace Lagdo\DbAdmin\Driver\Entity;

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
}
