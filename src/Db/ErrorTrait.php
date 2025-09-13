<?php

namespace Lagdo\DbAdmin\Driver\Db;

trait ErrorTrait
{
    /**
     * The last error code
     *
     * @var int
     */
    protected $errno = 0;

    /**
     * The last error message
     *
     * @var string
     */
    protected $error = '';

    /**
     * @inheritDoc
     */
    public function setError(string $error = '')
    {
        $this->error = $error;
    }

    /**
     * @inheritDoc
     */
    public function error(): string
    {
        return $this->error;
    }

    /**
     * @inheritDoc
     */
    public function hasError()
    {
        return $this->error !== '';
    }

    /**
     * @inheritDoc
     */
    public function setErrno(int $errno)
    {
        $this->errno = $errno;
    }

    /**
     * @inheritDoc
     */
    public function errorMessage()
    {
        return $this->errno !== 0 ? "({$this->errno}) {$this->error}" : $this->error;
    }
}
