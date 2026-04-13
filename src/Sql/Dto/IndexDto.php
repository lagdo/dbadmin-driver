<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

class IndexDto
{
    /**
     * @var string
     */
    public string $type = '';

    /**
     * @var string
     */
    public string $name = '';

    /**
     * @var string
     */
    public string $action = '';

    /**
     * @var string
     */
    public string $algorithm = '';

    /**
     * @var string
     */
    public string $partial = '';

    /**
     * @var array
     */
    public array $columns = [];

    /**
     * @var array
     */
    public array $lengths = [];

    /**
     * @var array
     */
    public array $descs = [];
}
