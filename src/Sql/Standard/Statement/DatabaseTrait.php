<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Statement;

use Lagdo\DbAdmin\Driver\Sql\DbProxyTrait;

use function preg_match;
use function strtoupper;
use function trim;
use function uniqid;

trait DatabaseTrait
{
    use DbProxyTrait;

    /**
     * @var bool
     */
    protected bool $setCharset = false;

    /**
     * Check if utf8mb4 might be needed
     *
     * @param string $create
     *
     * @return void
     */
    public function setUtf8mb4(string $create): void
    {
        // possible false positive
        if (!$this->setCharset && preg_match('~\butf8mb4~i', $create)) {
            $this->setCharset = true;
        }
    }

    /**
     * Get SET NAMES query, if utf8mb4 might be needed
     *
     * @return string
     */
    public function getCharsetQuery(): string
    {
        return !$this->setCharset ? '' : 'SET NAMES ' . $this->_engine()->charset() . ";\n\n";
    }

    /**
     * Command to update a view
     *
     * @param string $view The view name
     * @param array $values The view values
     *
     * @return array<string>
     */
    public function getUpdateViewQueries(string $view, array $values): array
    {
        // From view.inc.php
        $origType = 'VIEW';
        if ($this->_engine()->pgsql()) {
            $status = $this->_engine()->tableStatus($view);
            $origType = strtoupper($status->engine);
        }

        $name = trim($values['name']);
        $type = $values['materialized'] ? 'MATERIALIZED VIEW' : 'VIEW';
        $tempName = "{$name}_dbadmin_" . uniqid();

        $view = $this->_statement()->escapeTableName($view);
        $name = $this->_statement()->escapeTableName($name);
        $tempName = $this->_statement()->escapeTableName($tempName);
        return [
            "DROP $origType $view",
            "CREATE $type $name AS\n" . $values['select'],
            "DROP $type $name",
            "CREATE $type $tempName AS\n" . $values['select'],
            "DROP $type $tempName",
        ];
    }

    /**
     * Command to drop a view
     *
     * @param string $view The view name
     *
     * @return string
     */
    public function getDropViewQuery(string $view): string
    {
        // From view.inc.php
        $origType = 'VIEW';
        if ($this->_engine()->pgsql()) {
            $status = $this->_engine()->tableStatus($view);
            $origType = strtoupper($status->engine);
        }

        return "DROP $origType " . $this->_statement()->escapeTableName($view);
    }
}
