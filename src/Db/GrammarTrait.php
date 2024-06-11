<?php

namespace Lagdo\DbAdmin\Driver\Db;

use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;

use function substr;
use function str_replace;

trait GrammarTrait
{
    /**
     * @inheritDoc
     */
    public function escapeId(string $idf)
    {
        return $idf;
    }

    /**
     * @inheritDoc
     */
    public function unescapeId(string $idf)
    {
        $last = substr($idf, -1);
        return str_replace($last . $last, $last, substr($idf, 1, -1));
    }

    /**
     * @inheritDoc
     */
    public function convertField(TableFieldEntity $field)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function unconvertField(TableFieldEntity $field, string $value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function sqlForCreateTable(string $table, bool $autoIncrement, string $style)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function sqlForCreateIndex(string $table, string $type, string $name, string $columns)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function sqlForUseDatabase(string $database)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function sqlForForeignKeys(string $table)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function sqlForTruncateTable(string $table)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function sqlForCreateTrigger(string $table)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function autoIncrement()
    {
        return '';
    }
}
