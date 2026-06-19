<?php

namespace Lagdo\DbAdmin\Driver\Sql\Dto;

use Closure;

class QueryStreamDto
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
    public array $queryBuffer = [];

    /**
     * @var int
     */
    public int $queryCount = 0;

    /**
     * @var string
     */
    public string $queryDelimiter = ';';

    /**
     * @var string
     */
    public string $pregQueryDelimiter = ';';

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
    public string $functionDelimiterRegex = '';

    /**
     * @param Closure $queryLineReader
     */
    public function __construct(public Closure $queryLineReader)
    {}
}
