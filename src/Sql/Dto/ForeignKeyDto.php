<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

class ForeignKeyDto
{
    /**
     * @var string
     */
    public string $database = '';

    /**
     * @var string
     */
    public string $schema = '';

    /**
     * @var string
     */
    public string $table = '';

    /**
     * @var string
     */
    public string $definition = '';

    /**
     * @var array
     */
    public array $source = [];

    /**
     * @var array
     */
    public array $target = [];

    /**
     * @var string
     */
    public string $onUpdate = '';

    /**
     * @var string
     */
    public string $onDelete = '';

    /**
     * @var boolean
     */
    public bool $deferrable = false;
}
