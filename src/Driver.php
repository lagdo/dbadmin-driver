<?php

namespace Lagdo\DbAdmin\Driver;

use Lagdo\DbAdmin\Driver\Entity\ConfigEntity;
use Lagdo\DbAdmin\Driver\Utils\Utils;

use function in_array;

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
     * @inheritDoc
     */
    public function support(string $feature)
    {
        return in_array($feature, $this->config->features);
    }
}
