<?php

namespace Lagdo\DbAdmin\Driver\Fake;

use Lagdo\DbAdmin\Driver\Db\StatementInterface;
use Lagdo\DbAdmin\Driver\Entity\StatementFieldEntity;

use function count;
use function array_values;
use function array_pop;

/**
 * Fake Statement class for testing
 */
class Statement implements StatementInterface
{
    /**
     * The query result
     *
     * @var array
     */
    protected $results;

    /**
     * The constructor
     *
     * @param array $results
     */
    public function __construct(array $results)
    {
        $this->results = $results;
    }

    /**
     * @return array
     */
    public function rows(): array
    {
        return $this->results;
    }

    /**
     * @inheritDoc
     */
    public function rowCount(): int
    {
        return count($this->results);
    }

    /**
     * @inheritDoc
     */
    public function fetchAssoc(): array|null
    {
        return array_pop($this->results) ?: null;
    }

    /**
     * @inheritDoc
     */
    public function fetchRow(): array|null
    {
        return array_values(array_pop($this->results) ?: [null]);
    }

    /**
     * @inheritDoc
     */
    public function fetchField(): StatementFieldEntity|null
    {
        return null;
        // return new StatementFieldEntity();
    }
}
