<?php

namespace Lagdo\DbAdmin\Driver\Entity;

class UserEntity
{
    /**
     * @var string
     */
    public $name = '';

    /**
     * @var string
     */
    public $host = '';


    /**
     * @var string
     */
    public $password = '';

    /**
     * @var array
     */
    public $grants = [];

    /**
     * @var array
     */
    public $privileges = [];

    /**
     * The constructor
     *
     * @param string $name
     * @param string $host
     */
    public function __construct(string $name = '', string $host = '')
    {
        $this->name = $name;
        $this->host = $host;
    }
}
