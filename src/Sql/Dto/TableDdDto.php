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
     * @var string|null
     */
    public string|null $error = null;

    /**
     * @var array<ColumnDdDto>
     */
    private array $autoIncrementInputs;

    /**
     * @var array<ColumnDdDto>
     */
    private array $primaryKeyInputs;

    /**
     * @var ColumnDdDto|null
     */
    public readonly ColumnDdDto|null $addedAutoIncrementInput;

    /**
     * @var ColumnDdDto|null
     */
    public readonly ColumnDdDto|null $removedAutoIncrementInput;

    /**
     * @param array $inputs
     * @param array<ColumnDdDto> $columns
     * @param array<ForeignKeyDdDto> $foreignKeys
     */
    public function __construct(array $inputs = [], public readonly array $columns = [],
        public readonly array $foreignKeys = [])
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

        $this->setAutoIncrement();
    }

    /**
     * @return void
     */
    private function setAutoIncrement(): void
    {
        $addedAutoIncrementInput = null;
        $removedAutoIncrementInput = null;
        foreach ($this->columns as $input) {
            if ($input->autoIncrement && !$input->column->autoIncrement) {
                $addedAutoIncrementInput = $input;
            }
            if (!$input->autoIncrement && $input->column->autoIncrement) {
                $removedAutoIncrementInput = $input;
            }
        }
        $this->addedAutoIncrementInput = $addedAutoIncrementInput;
        $this->removedAutoIncrementInput = $removedAutoIncrementInput;
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
    abstract public function primaryKeyChanged(): bool;

    /**
     * @return array<ColumnDdDto>
     */
    public function addedColumns(): array
    {
        return array_filter($this->columns, fn(ColumnDdDto $column) => $column->added());
    }

    /**
     * @return array<ColumnDdDto>
     */
    public function editedColumns(): array
    {
        return [];
    }

    /**
     * @return array<ColumnDdDto>
     */
    public function droppedColumns(): array
    {
        return [];
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
    public function hasComment(): bool
    {
        return $this->comment !== null;
    }

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
        return array_filter($this->columns,
            fn(ColumnDdDto $column) => $column->added() || $column->edited());
    }

    /**
     * @return array<ColumnDdDto>
     */
    private function autoIncrementInputs(): array
    {
        return $this->autoIncrementInputs ??= array_values(array_filter($this->columns,
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
     * @return ColumnDdDto|null
     */
    public function autoIncrementInput(): ColumnDdDto|null
    {
        return $this->autoIncrementInputs()[0] ?? null;
    }

    /**
     * @return bool
     */
    public function autoIncrementChanged(): bool
    {
        return $this->addedAutoIncrementInput !== null ||
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
            $this->hasAutoIncrement();
    }

    /**
     * @return array<ColumnDdDto>
     */
    public function primaryKeyInputs(): array
    {
        return $this->primaryKeyInputs ??= array_values(array_filter($this->columns,
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
