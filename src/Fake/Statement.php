<?php

namespace Lagdo\DbAdmin\Driver\Fake;

use Lagdo\DbAdmin\Driver\Db\StatementInterface;

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
    public function rowCount()
    {
        return count($this->results);
    }

    /**
     * @inheritDoc
     */
    public function fetchAssoc()
    {
        return array_pop($this->results);
    }

    /**
     * @inheritDoc
     */
    public function fetchRow()
    {
        return array_values(array_pop($this->results));
    }

    /**
     * @inheritDoc
     */
    public function fetchField()
    {
        return null;
        // return new StatementFieldEntity();
    }
}
