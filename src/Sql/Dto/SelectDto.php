<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

use function array_filter;
use function array_map;
use function count;
use function implode;

class SelectDto
{
    /**
     * @var TableDto
     */
    public TableDto $table;

    /**
     * @param array<QueryClauseDto|null> $clauses
     * @param int $limit
     * @param int $offset
     * @param bool $grouped
     * @param array $groupBy
     */
    public function __construct(public array $clauses, public int $limit,
        public int $offset, public bool $grouped = false, public array $groupBy = [])
    {}

    /**
     * @param QueryClauseDto|null $clause
     *
     * @return bool
     */
    private function clauseIsValid(QueryClauseDto|null $clause): bool
    {
        return $clause !== null && count($clause->clauses) > 0;
    }

    /**
     * @param QueryClauseDto $clause
     *
     * @return string
     */
    private function clauseToString(QueryClauseDto $clause): string
    {
        return count($clause->clauses) === 0 ? '' :
            "{$clause->keyword} " . implode($clause->separator, $clause->clauses);
    }

    /**
     * @return string
     */
    public function query(): string
    {
        $clauses = array_filter($this->clauses, $this->clauseIsValid(...));
        return count($clauses) === 0 ? '' :
            implode(' ', array_map($this->clauseToString(...), $clauses));
    }
}
