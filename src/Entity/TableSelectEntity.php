<?php

namespace Lagdo\DbAdmin\Driver\Entity;

class TableSelectEntity
{
    /**
     * The table name
     *
     * @var string
     */
    public $table = '';

    /**
     * The fields to select
     *
     * @var array
     */
    public $fields = [];

    /**
     * The where clauses
     *
     * @var array
     */
    public $where = [];

    /**
     * The group by clauses
     *
     * @var array
     */
    public $group = [];

    /**
     * The order by clauses
     *
     * @var array
     */
    public $order = [];

    /**
     * All clauses, formatted
     *
     * @var string
     */
    public $clauses = '';

    /**
     * The row limit
     *
     * @var int
     */
    public $limit = 1;

    /**
     * The page number
     *
     * @var int
     */
    public $page = 0;

    /**
     * The constructor
     *
     * @param string $table
     * @param array $fields
     * @param array $where
     * @param array $group
     * @param array $order
     * @param int $limit
     * @param int $page
     */
    public function __construct(string $table, array $fields, array $where,
        array $group, array $order = [], int $limit = 1, int $page = 0)
    {
        $this->table = $table;
        $this->fields = $fields;
        $this->where = $where;
        $this->group = $group;
        $this->order = $order;
        $this->limit = $limit;
        $this->page = $page;
        if (!empty($where)) {
            $this->clauses = ' WHERE ' . \implode(' AND ', $where);
        }
        if (!empty($group) && $isGroup) {
            $this->clauses .= ' GROUP BY ' . \implode(', ', $group);
        }
        if (!empty($order)) {
            $this->clauses .= ' ORDER BY ' . \implode(', ', $order);
        }
    }
}
