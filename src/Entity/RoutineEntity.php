<?php

namespace Lagdo\DbAdmin\Driver\Entity;

class RoutineEntity
{
    /**
     * The constructor
     *
     * @param string $name
     * @param string $specificName
     * @param string $type
     * @param string $dtd
     */
    public function __construct(public string $name, public string $specificName,
        public string $type, public string $dtd)
    {
        $this->name = $name;
        $this->specificName = $specificName;
        $this->type = $type;
        $this->dtd = $dtd;
    }
}
