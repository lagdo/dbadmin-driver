<?php

namespace Lagdo\DbAdmin\Driver\Entity;

class Index
{
    /**
     * @var string
     */
    public $type = '';

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
