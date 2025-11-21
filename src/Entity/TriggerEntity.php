<?php

namespace Lagdo\DbAdmin\Driver\Entity;

class TriggerEntity
{
    /**
     * The constructor
     *
     * @param string $timing
     * @param string $event
     * @param string $of
     * @param string $statement
     * @param string $name
     * @param string $type
     * @param string $events
     */
    public function __construct(public string $timing = '', public string $event = '',
        public string $statement = '', public string $of = '', public string $name = '',
        public string $type = '', public string $events = '')
    {}
}
