<?php

namespace Lagdo\DbAdmin\Driver\Db\Pdo;

use Lagdo\DbAdmin\Driver\Db\Connection as AbstractConnection;
use Lagdo\DbAdmin\Driver\Db\Exception\AuthException;

use PDO;
use Exception;

abstract class Connection extends AbstractConnection
{
    // public function __construct() {
    //     $pos = array_search("SQL", $this->server->operators());
    //     if ($pos !== false) {
    //         unset($this->server->operators()[$pos]);
    //     }
    // }

    public function dsn($dsn, $username, $password, $options = [])
    {
        try {
            $this->client = new PDO($dsn, $username, $password, $options);
        } catch (Exception $ex) {
            // auth_error(h($ex->getMessage()));
            throw new AuthException($this->util->h($ex->getMessage()));
        }
        $this->client->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $this->client->setAttribute(PDO::ATTR_STATEMENT_CLASS, array(Statement::class));
    }

    /**
     * @inheritDoc
     */
    public function getServerInfo()
    {
        return @$this->client->getAttribute(PDO::ATTR_SERVER_VERSION);
    }

    public function quote($string)
    {
        return $this->client->quote($string);
    }

    public function query($query, $unbuffered = false)
    {
        $result = $this->client->query($query);
        $this->db->setError();
        if (!$result) {
            list(, $errno, $error) = $this->client->errorInfo();
            $this->db->setErrno($errno);
            $this->db->setError(($error) ? $error : $this->util->lang('Unknown error.'));
            return false;
        }
        $this->storedResult($result);
        return $result;
    }

    public function multiQuery($query)
    {
        return $this->_result = $this->query($query);
    }

    public function storedResult($result = null)
    {
        if (!$result) {
            $result = $this->_result;
            if (!$result) {
                return false;
            }
        }
        if ($result->columnCount()) {
            $result->num_rows = $result->rowCount(); // is not guaranteed to work with all drivers
            return $result;
        }
        $this->db->setAffectedRows($result->rowCount());
        return true;
    }

    public function nextResult()
    {
        if (!$this->_result) {
            return false;
        }
        $this->_result->_offset = 0;
        return @$this->_result->nextRowset(); // @ - PDO_PgSQL doesn't support it
    }

    public function result($query, $field = 0)
    {
        $result = $this->query($query);
        if (!$result) {
            return false;
        }
        $row = $result->fetch();
        return $row[$field];
    }

    /**
     * @inheritDoc
     */
    public function close()
    {
        $this->client = null;
    }
}
