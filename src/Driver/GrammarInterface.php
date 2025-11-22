<?php

namespace Lagdo\DbAdmin\Driver\Driver;

use Lagdo\DbAdmin\Driver\Db\GrammarInterface as DbGrammarInterface;
use Lagdo\DbAdmin\Driver\Entity\FieldType;
use Lagdo\DbAdmin\Driver\Entity\TableFieldEntity;
use Lagdo\DbAdmin\Driver\Entity\ForeignKeyEntity;
use Lagdo\DbAdmin\Driver\Entity\QueryEntity;

interface GrammarInterface extends DbGrammarInterface
{
    /**
     * Get escaped table name
     *
     * @param string $idf
     *
     * @return string
     */
    public function escapeTableName(string $idf);

    /**
     * Get select clause for convertible fields
     *
     * @param array $columns
     * @param array $fields
     * @param array $select
     *
     * @return string
     */
    public function convertFields(array $columns, array $fields, array $select = []);

    /**
     * Parse a string containing SQL queries
     *
     * @param QueryEntity $queryEntity
     *
     * @return bool
     */
    public function parseQueries(QueryEntity $queryEntity);

    /**
     * Get query to compute number of found rows
     *
     * @param string $table
     * @param array $where
     * @param bool $isGroup
     * @param array $groups
     *
     * @return string
     */
    public function getRowCountQuery(string $table, array $where, bool $isGroup, array $groups);

    /**
     * Remove current user definer from SQL command
     *
     * @param string $query
     *
     * @return string
     */
    public function removeDefiner(string $query): string;

    /**
     * Get default value clause
     *
     * @param TableFieldEntity $field
     *
     * @return string
     */
    public function getDefaultValueClause(TableFieldEntity $field);

    /**
     * Formulate SQL query with limit
     *
     * @param string $query Everything after SELECT
     * @param string $where Including WHERE
     * @param int $limit
     * @param int $offset
     *
     * @return string
     */
    public function getLimitClause(string $query, string $where, int $limit, int $offset = 0);

    /**
     * Format foreign key to use in SQL query
     *
     * @param ForeignKeyEntity $foreignKey
     *
     * @return string
     */
    public function formatForeignKey(ForeignKeyEntity $foreignKey);

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
     * Filter length value including enums
     *
     * @param string $length
     *
     * @return string
     */
    public function processLength(string $length): string;

    /**
     * Create SQL string from field type
     *
     * @param FieldType $field
     *
     * @return string
     */
    public function processType(FieldType $field, string $collate = "COLLATE"): string;

    /**
     * Create SQL string from field
     *
     * @param TableFieldEntity $field Basic field information
     * @param TableFieldEntity $typeField Information about field type
     *
     * @return array
     */
    public function processField(TableFieldEntity $field, TableFieldEntity $typeField): array;

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
}
