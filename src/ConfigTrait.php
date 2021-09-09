<?php

namespace Lagdo\DbAdmin\Driver;

trait ConfigTrait
{
    /**
     * Get the server jush
     * @return string
     */
    public function jush()
    {
        return $this->config()->jush;
    }

    /**
     * @return array
     */
    public function unsigned()
    {
        return $this->config()->unsigned;
    }

    /**
     * @return array
     */
    public function functions()
    {
        return $this->config()->functions;
    }

    /**
     * @return array
     */
    public function grouping()
    {
        return $this->config()->grouping;
    }

    /**
     * @return array
     */
    public function operators()
    {
        return $this->config()->operators;
    }

    /**
     * @return array
     */
    public function editFunctions()
    {
        return $this->config()->editFunctions;
    }

    /**
     * @return array
     */
    public function types()
    {
        return $this->config()->types;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function typeExists(string $type)
    {
        return isset($this->config()->types[$type]);
    }

    /**
     * @param string $type
     *
     * @return mixed
     */
    public function type(string $type)
    {
        return $this->config()->types[$type];
    }

    /**
     * @return array
     */
    public function structuredTypes()
    {
        return $this->config()->structuredTypes;
    }

    /**
     * @param string $key
     * @param mixed $value
     *
     * @return void
     */
    public function setStructuredType(string $key, $value)
    {
        $this->config()->structuredTypes[$key] = $value;
    }

    /**
     * @return array
     */
    public function onActions()
    {
        return \explode('|', $this->onActions);
    }
}
