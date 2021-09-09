<?php

namespace Lagdo\DbAdmin\Driver\Entity;

class IndexEntity
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
