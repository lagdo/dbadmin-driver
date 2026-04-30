<?php

namespace Lagdo\DbAdmin\Driver\Utils;

class Input
{
    /**
     * @var string
     */
    public $table = '';

    /**
     * @var array
     */
    public $values = [];

    /**
     * @inheritDoc
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @inheritDoc
     */
    public function hasTable(): bool
    {
        return $this->table !== '';
    }

    /**
     * @inheritDoc
     */
    public function getSelect(): array
    {
        return $this->values['select'] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getWhere(): array
    {
        return $this->values['where'] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getLimit(): int
    {
        return $this->values['limit'] ?? 0;
    }

    /**
     * @inheritDoc
     */
    public function getColumns(): array
    {
        return $this->values['columns'] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getAutoIncrementStep(): string
    {
        return $this->values['autoIncrementStep'] ?? '';
    }

    /**
     * @inheritDoc
     */
    public function getAutoIncrementColumn(): string
    {
        return $this->values['autoIncrementCol'] ?? '0';
    }

    /**
     * @inheritDoc
     */
    public function getChecks(): array
    {
        return $this->values['checks'] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getOverwrite(): bool
    {
        return $this->values['overwrite'] ?? false;
    }
}
