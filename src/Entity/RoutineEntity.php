<?php

namespace Lagdo\DbAdmin\Driver\Entity;

class RoutineEntity
{
    /**
     * @var string
     */
    public $name = '';

    /**
     * @var string
     */
    public $specificName = '';

    /**
     * @var string
     */
    public $type = '';

    /**
     * @var string
     */
    public $dtd = '';

    /**
     * The constructor
     *
     * @param string $name
     * @param string $specificName
     * @param string $type
     * @param string $dtd
     */
    public function __construct(string $name, string $specificName, string $type, string $dtd)
    {
        $this->name = $name;
        $this->specificName = $specificName;
        $this->type = $type;
        $this->dtd = $dtd;
    }
}
