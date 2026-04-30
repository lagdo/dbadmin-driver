<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

/**
 * Formatted inputs for a table column.
 */
class ColumnInputDto
{
    /**
     * @var string
     */
    public string $name = '';

    /**
     * @var string
     */
    public string $type = '';

    /**
     * @var string|null
     */
    public string|null $autoIncrement = null;

    /**
     * @var string
     */
    public string $defaultValue = '';

    /**
     * @var string
     */
    public string $nullValue = '';

    /**
     * @var string
     */
    public string $onUpdate = '';

    /**
     * @var string|null
     */
    public string|null $comment = null;

    /**
     * @var string
     */
    public string $after = '';

    /**
     * @return string
     */
    public function clauses(): string
    {
        $comment = $this->comment ?? '';
        return "{$this->name}{$this->type}{$this->nullValue}{$this->defaultValue}" .
            "{$this->onUpdate}{$comment}{$this->autoIncrement}";
    }

    /**
     * The constructor
     *
     * @param ColumnDto $column
     */
    public function __construct(public readonly ColumnDto $column)
    {}
}
