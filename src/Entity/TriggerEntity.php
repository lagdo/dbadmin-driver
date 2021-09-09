<?php

namespace Lagdo\DbAdmin\Driver\Entity;

class TriggerEntity
{
    /**
     * @var string
     */
    public $timing = '';

    /**
     * @var string
     */
    public $event = '';

    /**
     * The constructor
     *
     * @param string $timing
     * @param string $event
     */
    public function __construct(string $timing, string $event)
    {
        $this->timing = $timing;
        $this->event = $event;
    }
}
