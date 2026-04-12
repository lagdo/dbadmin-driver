<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Driver;

use Lagdo\DbAdmin\Support\Db\DbProxyTrait;
use Lagdo\DbAdmin\Support\Dto\UserDto;

use function preg_match;
use function version_compare;

trait ServerTrait
{
    use DbProxyTrait;

    /**
     * Get the user privileges
     *
     * @param UserDto $user
     *
     * @return void
     */
    public function getUserPrivileges(UserDto $user): void
    {
        $user->privileges = $this->_driver()->rows('SHOW PRIVILEGES');
    }

    /**
     * Check if connection has at least the given version
     *
     * @param string $version required version
     * @param string $mariaDb required MariaDB version
     *
     * @return bool
     */
    public function minVersion(string $version, string $mariaDb = ''): bool
    {
        $info = $this->_driver()->connection()?->serverInfo() ?? '';
        if ($mariaDb && preg_match('~([\d.]+)-MariaDB~', $info, $match)) {
            $info = $match[1];
            $version = $mariaDb;
        }
        return $version && version_compare($info, $version) >= 0;
    }

    /**
     * Get connection charset
     *
     * @return string
     */
    public function charset(): string
    {
        // SHOW CHARSET would require an extra query
        return $this->minVersion('5.5.3') ? 'utf8mb4' : 'utf8';
    }
}
