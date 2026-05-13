<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

use Closure;

use function array_filter;
use function array_values;

abstract class TableDdlDto
{
    /**
     * @var string
     */
    public string $name = '';

    /**
     * @var string
     */
    public string $engine = '';

    /**
     * @var string
     */
    public string $collation = '';

    /**
     * @var bool
     */
    public bool $hasAutoIncrement = false;

    /**
     * @var integer
     */
    public int $autoIncrement = 0;

    /**
     * @var bool
     */
    public bool $setComment = false;

    /**
     * @var string|null
     */
    public string|null $comment = null;

    /**
     * @var string
     */
    public string $partitioning = '';

    /**
     * @var array<ForeignKeyDto>
     */
    public array $foreignKeys = [];

    /**
     * @var array<ColumnDto>
     */
    private array $referencableColumns;

    /**
     * @var string|null
     */
    public string|null $error = null;

    /**
     * Columns to add, edit or drop.
     *
     * @var array<string, array<string|ColumnInputDto>>
     */
    public array $columns = [];

    /**
     * @var ColumnDto|null
     */
    public ColumnDto|null $autoIncrementColumn = null;

    /**
     * @var ColumnInputDto|null
     */
    public ColumnInputDto|null $enabledAutoIncrementInput = null;

    /**
     * @var ColumnInputDto|null
     */
    public ColumnInputDto|null $disabledAutoIncrementInput = null;

    /**
     * @param array $inputs
     * @param Closure $columnsGetter
     */
    public function __construct(array $inputs, private Closure $columnsGetter)
    {
        $this->name = $inputs['name'] ?? '';
        $this->engine = $inputs['engine'] ?? '';
        $this->collation = $inputs['collation'] ?? '';
        $this->setComment = $inputs['setComment'] ?? false;
        $this->hasAutoIncrement = $inputs['hasAutoIncrement'] ?? false;
        if ($this->hasAutoIncrement) {
            $this->autoIncrement = (int)($inputs['autoIncrement'] ?? 0);
        }
        if ($this->setComment) {
            $this->comment = $inputs['comment'] ?? '';
        }
        // $this->partitioning = $inputs['partitioning'] ?? '';
    }

    /**
     * @return void
     */
    public function clearColumns(): void
    {
        $this->columns = [];
    }

    /**
     * @return array<ColumnInputDto>
     */
    public function addedColumns(): array
    {
        return $this->columns[ColumnAction::ADD->value];
    }

    /**
     * @return array<ColumnDto>
     */
    public function getReferencableColumns(): array
    {
        return $this->referencableColumns ??= ($this->columnsGetter)($this->name);
    }

    /**
     * @return bool
     */
    public function hasAutoIncrement(): bool
    {
        return $this->autoIncrement > 0;
    }

    /**
     * @return bool
     */
    abstract public function engineChanged(): bool;

    /**
     * @return bool
     */
    abstract public function collationChanged(): bool;

    /**
     * @return bool
     */
    public function hasComment(): bool
    {
        return $this->comment !== null;
    }

    /**
     * @return string
     */
    abstract public function statusName(): string;

    /**
     * @return array<ColumnDto>
     */
    abstract public function statusColumns(): array;

    /**
     * @return bool
     */
    public function setupAutoIncrement(): bool
    {
        // Auto increment column in the table.
        $autoIncrementColumns = array_values(array_filter($this->statusColumns(),
            fn(ColumnDto $column) => $column->autoIncrement));
        $this->autoIncrementColumn = $autoIncrementColumns[0] ?? null;
        // Auto increment columns in the inputs.
        $inputColumns = [
            ...$this->columns[ColumnAction::ADD->value],
            ...($this->columns[ColumnAction::EDIT->value] ?? []),
        ];
        $enabledAutoIncrementInputs = array_values(array_filter($inputColumns,
            fn(ColumnInputDto $input) => $input->autoIncrement));
        $this->enabledAutoIncrementInput = $enabledAutoIncrementInputs[0] ?? null;
        $disabledAutoIncrementInputs = array_values(array_filter($inputColumns,
            fn(ColumnInputDto $input) => $input->autoIncrementDisabled()));
        $this->disabledAutoIncrementInput = $disabledAutoIncrementInputs[0] ?? null;

        return $this->autoIncrementDefined();
    }

    /**
     * @return bool
     */
    public function autoIncrementDefined(): bool
    {
        return $this->autoIncrementColumn !== null ||
            $this->enabledAutoIncrementInput !== null ||
            $this->disabledAutoIncrementInput !== null;
    }

    /**
     * @return bool
     */
    public function autoIncrementDisabled(): bool
    {
        return $this->disabledAutoIncrementInput !== null;
    }

    /**
     * @return bool
     */
    public function autoIncrementEnabled(): bool
    {
        return $this->enabledAutoIncrementInput !== null;
    }

    /**
     * @return bool
     */
    public function autoIncrementValueChanged(): bool
    {
        return !$this->autoIncrementEnabled() &&
            !$this->autoIncrementDisabled() &&
            $this->hasAutoIncrement() &&
            $this->autoIncrementColumn !== null;
    }
}
