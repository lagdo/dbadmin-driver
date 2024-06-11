<?php

namespace Lagdo\DbAdmin\Driver\Exception;

use Exception;

class AuthException extends Exception
{
    /**
     * The constructor
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
