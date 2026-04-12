<?php

namespace Lagdo\DbAdmin\Driver\Sql\Universal\Engine;

use Lagdo\DbAdmin\Driver\Sql\Dto\UserDto;

interface ServerInterface
{
    /**
     * Get the user privileges
     *
     * @param UserDto $user
     *
     * @return void
     */
    public function getUserPrivileges(UserDto $user): void;

    /**
     * Check if connection has at least the given version
     *
     * @param string $version required version
     * @param string $mariaDb required MariaDB version
     *
     * @return bool
     */
    public function minVersion(string $version, string $mariaDb = ''): bool;

    /**
     * Get connection charset
     *
     * @return string
     */
    public function charset(): string;
}
