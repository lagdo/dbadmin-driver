<?php

namespace Lagdo\DbAdmin\Driver\Entity;

class UserTypeEntity
{
    /**
     * The constructor
     *
     * @param string $oid
     * @param string $name
     * @param array<string> $enums
     */
    public function __construct(public string $oid,
        public string $name, public array $enums = [])
    {}
}
