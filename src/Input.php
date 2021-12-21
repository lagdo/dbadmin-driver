<?php

namespace Lagdo\DbAdmin\Driver;

class Input implements InputInterface
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
        if (!isset($this->values['select'])) {
            return [];
        }
        return $this->values['select'];
    }

    /**
     * @inheritDoc
     */
    public function getWhere(): array
    {
        if (!isset($this->values['where'])) {
            return [];
        }
        return $this->values['where'];
    }

    /**
     * @inheritDoc
     */
    public function getLimit(): int
    {
        if (!isset($this->values['limit'])) {
            return 0;
        }
        return $this->values['limit'];
    }

    /**
     * @inheritDoc
     */
    public function getFields(): array
    {
        if (!isset($this->values['fields'])) {
            return [];
        }
        return $this->values['fields'];
    }

    /**
     * @inheritDoc
     */
    public function getAutoIncrementStep(): string
    {
        if (!isset($this->values['autoIncrementStep'])) {
            return '';
        }
        return $this->values['autoIncrementStep'];
    }

    /**
     * @inheritDoc
     */
    public function getAutoIncrementField(): string
    {
        if (!isset($this->values['autoIncrementCol'])) {
            return '0';
        }
        return $this->values['autoIncrementCol'];
    }

    /**
     * @inheritDoc
     */
    public function getChecks(): array
    {
        if (!isset($this->values['checks'])) {
            return [];
        }
        return $this->values['checks'];
    }

    /**
     * @inheritDoc
     */
    public function getOverwrite(): bool
    {
        if (!isset($this->values['overwrite'])) {
            return false;
        }
        return $this->values['overwrite'];
    }
}
