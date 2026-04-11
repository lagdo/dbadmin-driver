<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Driver;

use Lagdo\DbAdmin\Support\Db\AbstractDelegate;
use Lagdo\DbAdmin\Support\Dto\UserDto;

use function preg_match;
use function version_compare;

class Server extends AbstractDelegate implements ServerInterface
{
    /**
     * @inheritDoc
     */
    public function getUserPrivileges(UserDto $user): void
    {
        $user->privileges = $this->driver->rows('SHOW PRIVILEGES');
    }

    /**
     * @inheritDoc
     */
    public function minVersion(string $version, string $mariaDb = ''): bool
    {
        $info = $this->driver->connection()?->serverInfo() ?? '';
        if ($mariaDb && preg_match('~([\d.]+)-MariaDB~', $info, $match)) {
            $info = $match[1];
            $version = $mariaDb;
        }
        return $version && version_compare($info, $version) >= 0;
    }

    /**
     * @inheritDoc
     */
    public function charset(): string
    {
        // SHOW CHARSET would require an extra query
        return $this->minVersion('5.5.3') ? 'utf8mb4' : 'utf8';
    }
}
