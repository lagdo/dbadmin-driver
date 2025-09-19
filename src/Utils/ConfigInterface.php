<?php

namespace Lagdo\DbAdmin\Driver\Utils;

/**
 * DB server options
 */
interface ConfigInterface
{
    /**
     * Get the driver name
     * 
     * @return string
     */
    public function driver(): string;

    /**
     * Get the driver options
     * 
     * @return array
     */
    public function options(): array;
}
