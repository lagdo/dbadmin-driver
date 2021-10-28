<?php

namespace Lagdo\DbAdmin\Driver\Entity;

class TriggerEntity
{
    /**
     * @var string
     */
    public $name = '';

    /**
     * @var string
     */
    public $timing = '';

    /**
     * @var string
     */
    public $event = '';

    /**
     * @var string
     */
    public $statement = '';

    /**
     * @var string
     */
    public $of = '';

    /**
     * The constructor
     *
     * @param string $timing
     * @param string $event
     * @param string $of
     * @param string $statement
     * @param string $name
     */
    public function __construct(string $timing = '', string $event = '',
        string $statement = '', string $of = '', string $name = '')
    {
        $this->timing = $timing;
        $this->event = $event;
    }
}
