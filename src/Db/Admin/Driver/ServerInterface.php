<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Driver;

use Lagdo\DbAdmin\Support\Dto\UserDto;

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
