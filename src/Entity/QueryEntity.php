<?php

namespace Lagdo\DbAdmin\Driver\Entity;

class QueryEntity
{
    /**
     * The SQL queries to parse.
     *
     * @var string
     */
    public $queries = '';

    /**
     * The last parsed SQL query.
     *
     * @var string
     */
    public $query = '';

    /**
     * @var string
     */
    public $delimiter = ';';

    /**
     * @var int
     */
    public $offset = 0;

    /**
     * @var int
     */
    public $limit = 0;

    /**
     * @var bool
     */
    public $errorStops = 0;

    /**
     * @var bool
     */
    public $onlyErrors = 0;

    /**
     * The constructor
     *
     * @param string $queries
     */
    public function __construct(string $queries, int $limit, bool $errorStops, bool $onlyErrors)
    {
        $this->queries = $queries;
        $this->limit = $limit;
        $this->errorStops = $errorStops;
        $this->onlyErrors = $onlyErrors;
    }
}
