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
     * @param string $error
     *
     * @return void
     */
    public function setError(string $error = ''): void
    {
        $this->error = $error;
    }

    /**
     * @param int $errno
     *
     * @return void
     */
    public function setErrno(int $errno): void
    {
        $this->errno = $errno;
    }

    /**
     * @return string
     */
    public function error(): string
    {
        return $this->error;
    }

    /**
     * @return bool
     */
    public function hasError(): bool
    {
        return $this->error !== '';
    }

    /**
     * @return string
     */
    public function errorMessage(): string
    {
        return $this->errno !== 0 ? "({$this->errno}) {$this->error}" : $this->error;
    }
}
