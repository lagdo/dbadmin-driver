<?php

namespace Lagdo\DbAdmin\Driver\Dto;

class ForeignKeyDto
{
    /**
     * @var string
     */
    public $database = '';

    /**
     * @var string
     */
    public $schema = '';

    /**
     * @var string
     */
    public $table = '';

    /**
     * @var string
     */
    public $definition = '';

    /**
     * @var array
     */
    public $source = [];

    /**
     * @var array
     */
    public $target = [];

    /**
     * @var string
     */
    public $onUpdate = '';

    /**
     * @var string
     */
    public $onDelete = '';

    /**
     * @var boolean
     */
    public $deferrable = false;
}
