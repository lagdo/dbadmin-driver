<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

/**
 * A column in a statement resultset row.
 */
class ResultColumnDto
{
    /**
     * @param string $type
     * @param boolean $isBinary
     * @param string $name
     * @param string $orgName
     * @param string $table
     * @param string $orgTable
     */
    public function __construct(protected string $type, protected bool $isBinary,
        protected string $name, protected string $orgName = '',
        protected string $table = '', protected string $orgTable = '')
    {}

    public function type(): string
    {
        return $this->type;
    }

    public function isBinary(): bool
    {
        return $this->isBinary;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function orgName(): string
    {
        return $this->orgName;
    }

    public function table(): string
    {
        return $this->table;
    }

    public function orgTable(): string
    {
        return $this->orgTable;
    }

    public function tableName(): string
    {
        return $this->table ?? $this->orgTable;
    }
}
