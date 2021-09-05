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
            throw new AuthException($this->util->html($ex->getMessage()));
        }
        $this->client->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $this->client->setAttribute(PDO::ATTR_STATEMENT_CLASS, array(Statement::class));
    }

    /**
     * @inheritDoc
     */
    public function serverInfo()
    {
        return @$this->client->getAttribute(PDO::ATTR_SERVER_VERSION);
    }

    public function quote($string)
    {
        return $this->client->quote($string);
    }

    public function query($query, $unbuffered = false)
    {
        $statement = $this->client->query($query);
        $this->db->setError();
        if (!$statement) {
            list(, $errno, $error) = $this->client->errorInfo();
            $this->db->setErrno($errno);
            $this->db->setError(($error) ? $error : $this->util->lang('Unknown error.'));
            return false;
        }
        // rowCount() is not guaranteed to work with all drivers
        if (($statement->numRows = $statement->rowCount()) > 0) {
            $this->db->setAffectedRows($statement->numRows);
        }
        return $statement;
    }

    public function multiQuery($query)
    {
        return $this->statement = $this->query($query);
    }

    public function storedResult()
    {
        if (!$this->statement) {
            return false;
        }
        // rowCount() is not guaranteed to work with all drivers
        if (($this->statement->numRows = $this->statement->rowCount()) > 0) {
            $this->db->setAffectedRows($this->statement->numRows);
        }
        return $this->statement;
    }

    public function nextResult()
    {
        if (!$this->statement) {
            return false;
        }
        $this->statement->offset = 0;
        return @$this->statement->nextRowset(); // @ - PDO_PgSQL doesn't support it
    }

    public function result($query, $field = 0)
    {
        $statement = $this->query($query);
        if (!$statement) {
            return false;
        }
        $row = $statement->fetch();
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
