<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Driver;

use Lagdo\DbAdmin\Support\Dto\UserDto;

trait ServerTrait
{
    /**
     * @var ServerInterface
     */
    private ServerInterface $server;

    /**
     * @return ServerInterface
     */
    private function _s(): ServerInterface
    {
        return $this->server ??= new Server($this, $this->grammar(), $this->utils);
    }

    /**
     * Get the user privileges
     *
     * @param UserDto $user
     *
     * @return void
     */
    public function getUserPrivileges(UserDto $user): void
    {
        $this->_s()->getUserPrivileges($user);
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
        return $this->_s()->minVersion($version, $mariaDb);
    }

    /**
     * Get connection charset
     *
     * @return string
     */
    public function charset(): string
    {
        return $this->_s()->charset();
    }
}
