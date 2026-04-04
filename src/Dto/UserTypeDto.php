<?php

namespace Lagdo\DbAdmin\Support\Dto;

class UserTypeDto
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
