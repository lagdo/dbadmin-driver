<?php

namespace Lagdo\DbAdmin\Driver\Sql\Standard\Statement;

use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnDto;
use Lagdo\DbAdmin\Driver\Sql\Dto\ColumnType;

interface StatementInterface
{
    /**
     * Check if utf8mb4 might be needed
     *
     * @param string $create
     *
     * @return void
     */
    public function setUtf8mb4(string $create): void;

    /**
     * Get SET NAMES query, if utf8mb4 might be needed
     *
     * @return string
     */
    public function getCharsetQuery(): string;

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

    /**
     * Command to update a view
     *
     * @param string $view The view name
     * @param array $values The view values
     *
     * @return array<string>
     */
    public function getUpdateViewQueries(string $view, array $values): array;

    /**
     * Command to drop a view
     *
     * @param string $view The view name
     *
     * @return string
     */
    public function getDropViewQuery(string $view): string;

    /**
     * Get query to compute number of found rows
     *
     * @param string $table
     * @param array $where
     * @param bool $grouped
     * @param array $groupBy
     *
     * @return string
     */
    public function getRowCountQuery(string $table, array $where, bool $grouped, array $groupBy): string;

    /**
     * Build a query to insert data into table
     *
     * @param string $table
     * @param array $values Escaped columns in keys, quoted data in values
     *
     * @return string
     */
    public function getInsertRowQuery(string $table, array $values): string;

    /**
     * Get select clause for convertible columns
     *
     * @param array $names
     * @param array $columns
     * @param array $select
     *
     * @return string
     */
    public function convertColumns(array $names, array $columns, array $select = []): string;
}
