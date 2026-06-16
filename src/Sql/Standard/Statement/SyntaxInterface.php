<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Statement;

use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnType;

interface SyntaxInterface
{
    /**
     * Get escaped table name
     *
     * @param string $idf
     *
     * @return string
     */
    public function escapeTableName(string $idf): string;

    /**
     * Escape or unescape string to use inside form []
     *
     * @param string $idf
     * @param bool $back
     *
     * @return string
     */
    public function bracketEscape(string $idf, bool $back = false): string;

    /**
     * Escape column key used in where()
     *
     * @param string
     *
     * @return string
     */
    public function escapeKey(string $key): string;

    /**
     * Remove current user definer from SQL command
     *
     * @param string $query
     *
     * @return string
     */
    public function removeDefiner(string $query): string;

    /**
     * Filter length value including enums
     *
     * @param string $length
     *
     * @return string
     */
    public function processLength(string $length): string;

    /**
     * @param ColumnDto $column Single column from columns()
     * @param string $value
     * @param string $function
     *
     * @return string
     */
    public function getUnconvertedFieldValue(ColumnDto $column,
        string $value, string $function = ''): string;

    /**
     * Create SQL string from column type
     *
     * @param ColumnType $column
     *
     * @return string
     */
    public function getColumnType(ColumnType $column, string $collate = "COLLATE"): string;
}
