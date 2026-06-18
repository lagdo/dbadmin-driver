<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

use Closure;

class QueryCodeDto
{
    /**
     * @var string
     */
    public string $queryLine = '';

    /**
     * @var string
     */
    public string $inputLine = '';

    /**
     * @var int
     */
    public int $lineNumber = 0;

    /**
     * @var array<string>
     */
    public array $queryLines = [];

    /**
     * @var int
     */
    public int $queryCount = 0;

    /**
     * @var int
     */
    public int $errorCount = 0;

    /**
     * @var string
     */
    public string $queryDelimiter = ';';

    /**
     * @var bool
     */
    public bool $inMultilineComment = false;

    /**
     * @var bool
     */
    public bool $inMultilineString = false;

    /**
     * @var bool
     */
    public bool $inMultilineFunction = false;

    /**
     * @var string
     */
    public string $functionDelimiter = '';

    /**
     * @param Closure $queryLineReader
     */
    public function __construct(public Closure $queryLineReader)
    {}
}
