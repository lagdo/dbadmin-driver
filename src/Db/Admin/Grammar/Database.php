<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Grammar;

use Lagdo\DbAdmin\Support\Db\AbstractDelegate;

use function preg_match;
use function strtoupper;
use function trim;
use function uniqid;

class Database extends AbstractDelegate implements DatabaseInterface
{
    /**
     * @var bool
     */
    protected $setCharset = false;

    /**
     * @inheritDoc
     */
    public function setUtf8mb4(string $create): void
    {
        // possible false positive
        if (!$this->setCharset && preg_match('~\butf8mb4~i', $create)) {
            $this->setCharset = true;
        }
    }

    /**
     * @inheritDoc
     */
    public function getCharsetQuery(): string
    {
        return !$this->setCharset ? '' : 'SET NAMES ' . $this->driver->charset() . ";\n\n";
    }

    /**
     * @inheritDoc
     */
    public function getUpdateViewQueries(string $view, array $values): array
    {
        // From view.inc.php
        $origType = 'VIEW';
        if ($this->driver->pgsql()) {
            $status = $this->driver->tableStatus($view);
            $origType = strtoupper($status->engine);
        }

        $name = trim($values['name']);
        $type = $values['materialized'] ? 'MATERIALIZED VIEW' : 'VIEW';
        $tempName = "{$name}_dbadmin_" . uniqid();

        $view = $this->grammar->escapeTableName($view);
        $name = $this->grammar->escapeTableName($name);
        $tempName = $this->grammar->escapeTableName($tempName);
        return [
            "DROP $origType $view",
            "CREATE $type $name AS\n" . $values['select'],
            "DROP $type $name",
            "CREATE $type $tempName AS\n" . $values['select'],
            "DROP $type $tempName",
        ];
    }

    /**
     * @inheritDoc
     */
    public function getDropViewQuery(string $view): string
    {
        // From view.inc.php
        $origType = 'VIEW';
        if ($this->driver->pgsql()) {
            $status = $this->driver->tableStatus($view);
            $origType = strtoupper($status->engine);
        }

        return "DROP $origType " . $this->grammar->escapeTableName($view);
    }
}
