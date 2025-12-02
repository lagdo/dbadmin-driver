<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Db\Connection;
use Lagdo\DbAdmin\Driver\Db\ConnectionInterface;
use Lagdo\DbAdmin\Driver\Entity\ConfigEntity;
use Lagdo\DbAdmin\Driver\Exception\AuthException;
use Lagdo\DbAdmin\Driver\Utils\Utils;
use Closure;

use function preg_match;
use function version_compare;

abstract class Driver implements DriverInterface
{
    use Driver\ConfigTrait;
    use Driver\ConnectionTrait;
    use Driver\ServerTrait;
    use Driver\TableTrait;
    use Driver\DatabaseTrait;
    use Driver\QueryTrait;
    use Driver\GrammarTrait;

    /**
     * @var array
     */
    private static array $drivers = [];

    /**
     * @var Connection
     */
    protected $connection = null;

    /**
     * @var ConnectionInterface
     */
    protected $mainConnection = null;

    /**
     * The constructor
     *
     * @param Utils $utils
     * @param array $options
     */
    public function __construct(protected Utils $utils, array $options)
    {
        $this->config = new ConfigEntity($utils->trans, $options);
        $this->beforeConnection();
        // Create and set the main connection.
        $this->connection = $this->createConnection($options);
    }

    /**
     * @param string $driver
     * @param Closure $closure
     *
     * @return void
     */
    public static function registerDriver(string $driver, Closure $closure): void
    {
        self::$drivers[$driver] = $closure;
    }

    /**
     * @return array<Closure>
     */
    public static function drivers(): array
    {
        return self::$drivers;
    }

    /**
     * @return void
     */
    protected function beforeConnection()
    {}

    /**
     * @return void
     */
    protected function configConnection()
    {}

    /**
     * @return void
     */
    protected function connectionOpened()
    {}

    /**
     * @inheritDoc
     */
    public function connection(): ConnectionInterface|null
    {
        return $this->connection;
    }

    /**
     * @inheritDoc
     * @throws AuthException
     */
    public function openConnection(string $database, string $schema = ''): ConnectionInterface
    {
        if (!$this->connection->open($database, $schema)) {
            throw new AuthException($this->error());
        }

        $this->config->database = $database;
        $this->config->schema = $schema;
        if ($this->mainConnection === null) {
            $this->mainConnection = $this->connection;
            $this->configConnection();
        }
        $this->connectionOpened();
        return $this->connection;
    }

    /**
     * @inheritDoc
     */
    public function closeConnection(): void
    {
        $this->connection->close();
        $this->connection = null;
    }

    /**
     * @inheritDoc
     */
    public function newConnection(string $database, string $schema = ''): ConnectionInterface|null
    {
        $connection = $this->createConnection($this->config->options());
        return !$connection || !$connection->open($database, $schema) ? null : $connection;
    }

    /**
     * @inheritDoc
     */
    public function minVersion(string $version, string $mariaDb = ''): bool
    {
        $info = $this->connection()?->serverInfo() ?? '';
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
