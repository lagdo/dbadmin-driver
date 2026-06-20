<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

use Closure;

use function array_filter;
use function array_map;
use function array_values;
use function count;
use function implode;

abstract class TableDdDto
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
     * @var array<string, array<string|ColumnDdDto>>
     */
    public array $columns = [];

    /**
     * @var array<ColumnDdDto>
     */
    private array $autoIncrementInputs;

    /**
     * @var array<ColumnDdDto>
     */
    private array $primaryKeyInputs;

    /**
     * @var ColumnDto|null
     */
    public ColumnDto|null $autoIncrementColumn = null;

    /**
     * @var ColumnDdDto|null
     */
    public ColumnDdDto|null $addedAutoIncrementInput = null;

    /**
     * @var ColumnDdDto|null
     */
    public ColumnDdDto|null $removedAutoIncrementInput = null;

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
     * @return array<ColumnDdDto>
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
     * @return ColumnDto|null
     */
    public function statusAutoIncrementColumn(): ColumnDto|null
    {
        $columns = array_values(array_filter($this->statusColumns(),
            fn(ColumnDto $column) => $column->autoIncrement));
        return $columns[0] ?? null;
    }

    /**
     * @return array<ColumnDdDto>
     */
    public function columns(): array
    {
        return [
            ...$this->columns[ColumnAction::ADD->value],
            ...($this->columns[ColumnAction::EDIT->value] ?? []),
        ];
    }

    /**
     * @return array<ColumnDdDto>
     */
    public function autoIncrementInputs(): array
    {
        return $this->autoIncrementInputs ??= array_values(array_filter($this->columns(),
            fn(ColumnDdDto $input) => $input->autoIncrement));
    }

    /**
     * @return int
     */
    public function autoIncrementColumnCount(): int
    {
        return count($this->autoIncrementInputs());
    }

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
        $this->addedAutoIncrementInput = $this->autoIncrementInputs()[0] ?? null;
        $removedAutoIncrementInputs = array_values(array_filter($this->columns(),
            fn(ColumnDdDto $input) => $input->autoIncrementRemoved()));
        $this->removedAutoIncrementInput = $removedAutoIncrementInputs[0] ?? null;

        return $this->autoIncrementChanged();
    }

    /**
     * @return bool
     */
    public function autoIncrementChanged(): bool
    {
        return $this->autoIncrementColumn !== null ||
            $this->addedAutoIncrementInput !== null ||
            $this->removedAutoIncrementInput !== null;
    }

    /**
     * @return bool
     */
    public function autoIncrementRemoved(): bool
    {
        return $this->removedAutoIncrementInput !== null;
    }

    /**
     * @return bool
     */
    public function autoIncrementAdded(): bool
    {
        return $this->addedAutoIncrementInput !== null;
    }

    /**
     * @return bool
     */
    public function autoIncrementValueChanged(): bool
    {
        return !$this->autoIncrementAdded() &&
            !$this->autoIncrementRemoved() &&
            $this->hasAutoIncrement() &&
            $this->autoIncrementColumn !== null;
    }

    /**
     * @return array<ColumnDdDto>
     */
    public function primaryKeyInputs(): array
    {
        return $this->primaryKeyInputs ??= array_values(array_filter($this->columns(),
            fn(ColumnDdDto $input) => $input->primary));
    }

    /**
     * @return int
     */
    public function primaryKeyColumnCount(): int
    {
        return count($this->primaryKeyInputs());
    }

    /**
     * @param Closure $escapeName
     *
     * @return string
     */
    public function primaryKeyClause(Closure $escapeName): string
    {
        $columnNames = implode(', ', array_map(fn(ColumnDdDto $input) =>
            $escapeName($input->name), $this->primaryKeyInputs()));
        return "PRIMARY KEY ($columnNames)";
    }
}
