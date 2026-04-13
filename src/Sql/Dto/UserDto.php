<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

class UserDto
{
    /**
     * @var string
     */
    public string $name = '';

    /**
     * @var string
     */
    public string $host = '';


    /**
     * @var string
     */
    public string $password = '';

    /**
     * @var array
     */
    public array $grants = [];

    /**
     * @var array
     */
    public array $privileges = [];

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
