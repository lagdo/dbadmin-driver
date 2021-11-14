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
     * The constructor
     *
     * @param string $queries
     */
    public function __construct(string $queries)
    {
        $this->queries = $queries;
    }
}
