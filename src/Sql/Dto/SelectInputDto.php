<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

use function count;
use function implode;

class SelectInputDto
{
    /**
     * All clauses, formatted
     *
     * @var string
     */
    public string $clauses = '';

    /**
     * The constructor
     *
     * @param string $table
     * @param array $columns
     * @param array $where
     * @param array $group
     * @param array $order
     * @param int $limit
     * @param int $page
     */
    public function __construct(public string $table, public array $columns,
        public array $where, public array $group, public array $order = [],
        public int $limit = 1, public int $page = 0)
    {
        if (!empty($where)) {
            $this->clauses = ' WHERE ' . implode(' AND ', $where);
        }
        if (!empty($group) && count($group) < count($columns)) {
            $this->clauses .= ' GROUP BY ' . implode(', ', $group);
        }
        if (!empty($order)) {
            $this->clauses .= ' ORDER BY ' . implode(', ', $order);
        }
    }
}
