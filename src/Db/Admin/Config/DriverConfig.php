<?php

namespace Lagdo\DbAdmin\Support\Db\Admin\Config;

use Lagdo\DbAdmin\Support\Utils\TranslatorInterface;

use function explode;

class DriverConfig
{
    /**
     * @var string
     */
    public $jush = '';

    /**
     * @var string
     */
    public $version = '0.23.0';

    /**
     * @var array
     */
    public $drivers = [];

    /**
     * @var array
     */
    public $types = [];

    /**
     * @var array
     */
    public $features = [];

    /**
     * @var array
     */
    public $unsigned = [];

    /**
     * @var array
     */
    public $operators = [];

    /**
     * @var array
     */
    public $functions = [];

    /**
     * @var array
     */
    public $grouping = [];

    /**
     * @var array
     */
    public $insertFunctions = [];

    /**
     * @var array
     */
    public $editFunctions = [];

    /**
     * From bootstrap.inc.php
     * /// used in foreignKeys()
     * @var string
     */
    public $actions = 'RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT';

    /**
     * // not point, not interval
     * @var string
     */
    public $numberRegex = '((?<!o)int(?!er)|numeric|real|float|double|decimal|money)';

    /**
     * From index.php
     * @var string
     */
    public $inout = 'IN|OUT|INOUT';

    /**
     * Regex to parse SQL statements in a text
     *
     * @var string
     */
    public $sqlStatementRegex = '';
    // Original code from Adminer
    // {
    //     $parse = '[\'"' .
    //         ($this->driver->sql() ? '`#' :
    //         ($this->driver->sqlite() ? '`[' :
    //         ($this->driver->mssql() ? '[' : ''))) . ']|/\*|-- |$' .
    //         ($this->driver->pgsql() ? '|\$[^$]*\$' : '');
    //     return "\\s*|$parse";
    // }

    /**
     * From index.php
     * @var string
     */
    public $enumLengthRegex = "'(?:''|[^'\\\\]|\\\\.)*'";

    /**
     * @var array
     */
    public $partitionBy = [];

    /**
     * @var array
     */
    public $generated = [];

    /**
     * The current database name
     *
     * @var string
     */
    public $database = '';

    /**
     * The current schema name
     *
     * @var string
     */
    public $schema = '';

    /**
     * The constructor
     *
     * @param TranslatorInterface $trans
     * @param array $options
     */
    public function __construct(public TranslatorInterface $trans, public array $options)
    {}

    /**
     * @param string $database
     * @param string $schema
     *
     * @return void
     */
    public function setDatabase(string $database, string $schema): void
    {
        $this->database = $database;
        $this->schema = $schema;
    }

    /**
     * Get the driver options
     *
     * @return array
     */
    public function options(): array
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function onActions(): array
    {
        return explode('|', $this->actions);
    }
}
