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
    public function getCreateTableQuery(string $table, bool $autoIncrement, string $style)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getCreateIndexQuery(string $table, string $type, string $name, string $columns)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getUseDatabaseQuery(string $database)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getForeignKeysQuery(string $table)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getTruncateTableQuery(string $table)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getCreateTriggerQuery(string $table)
    {
        return '';
    }

    /**
     * @inheritDoc
     */
    public function getAutoIncrementModifier()
    {
        return '';
    }
}
