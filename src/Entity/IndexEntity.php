<?php

namespace Lagdo\DbAdmin\Driver\Entity;

class IndexEntity
{
    /**
     * @var string
     */
    public $type = '';

    /**
     * @var string
     */
    public $name = '';

    /**
     * @var string
     */
    public $action = '';

    /**
     * @var array
     */
    public $columns = [];

    /**
     * @var array
     */
    public $lengths = [];

    /**
     * @var array
     */
    public $descs = [];
}
