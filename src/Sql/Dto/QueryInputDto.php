<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

class QueryInputDto
{
    /**
     * The SQL queries to parse.
     *
     * @var string
     */
    public string $queries = '';

    /**
     * The last parsed SQL query.
     *
     * @var string
     */
    public string $query = '';

    /**
     * @var string
     */
    public string $delimiter = ';';

    /**
     * @var int
     */
    public int $offset = 0;

    /**
     * @var int
     */
    public int $limit = 0;

    /**
     * @var bool
     */
    public bool $errorStops = false;

    /**
     * @var bool
     */
    public bool $onlyErrors = false;

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
