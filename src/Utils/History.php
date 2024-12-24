<?php

namespace Lagdo\DbAdmin\Driver\Utils;

use function intval;
use function microtime;
use function preg_match;

class History
{
    /**
     * Executed queries
     *
     * @var array
     */
    protected $queries = [];

    /**
     * Save a query in the history
     *
     * @param string $query
     *
     * @return void
     */
    public function save(string $query)
    {
        $this->queries[] = [
            'start' => intval(microtime(true)),
            'query' => (preg_match('~;$~', $query) ? "DELIMITER ;;\n$query;\nDELIMITER " : $query),
        ];
    }

    /**
     * Get the remembered queries
     *
     * @return array
     */
    public function queries()
    {
        return $this->queries;
    }
}
