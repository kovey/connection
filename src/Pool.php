<?php
/**
 * @description connection pool
 *
 * @package Connection
 *
 * @author kovey
 *
 * @time 2020-04-20 16:32:41
 *
 */
namespace Kovey\Connection;

use Kovey\Connection\Pool\PoolInterface;
use Kovey\Connection\Pool\Redis;
use Kovey\Db\DbInterface;
use Kovey\Redis\RedisInterface;
use Kovey\Db\Exception\DbException;
use Kovey\Db\ForUpdate\Type;
use Kovey\Db\Sql\Update;
use Kovey\Db\Sql\Insert;
use Kovey\Db\Sql\Select;
use Kovey\Db\Sql\Delete;
use Kovey\Db\Sql\BatchInsert;
use Kovey\Db\Sql\Where;
use Kovey\Library\Trace\TraceInterface;

class Pool implements ManualCollectInterface, DbInterface, TraceInterface
{
    /**
     * @description pool
     *
     * @var PoolInterface
     */
    private PoolInterface $pool;

    /**
     * @description connection
     *
     * @var Redis | Mysql
     */
    private RedisInterface | DbInterface $connection;

    /**
     * @description is collect
     *
     * @var bool
     */
    private bool $isCollected = false;

    /**
     * @description is redis
     *
     * @var bool
     */
    private bool $isRedis = false;

    private string $traceId;

    private string $spanId;

    /**
     * @description construct
     *
     * @param PortInterface $pool
     *
     * @return Pool
     */
    public function __construct(PoolInterface $pool)
    {
        $this->pool = $pool;
        $this->isRedis = $this->pool instanceof Redis;
    }

    /**
     * @description connection
     *
     * @return RedisInterface | DbInterface
     */
    public function getConnection() : RedisInterface | DbInterface
    {
        $this->initConnection();

        return $this->connection;
    }

    /**
     * @description init connection
     *
     * @return void
     */
    public function initConnection() : void
    {
        if (!empty($this->connection)) {
            return;
        }

        $connection = $this->pool->pop();
        if (empty($connection)) {
            throw new DbException('pool is empty', 1012);
        }

        $this->connection = $connection;
        $this->connection->setTraceId($this->traceId);
        $this->connection->setSpanId($this->spanId);
    }

    /**
     * @description check database
     *
     * @return void
     */
    private function checkDatabase() : void
    {
        $this->initConnection();

        if (!$this->connection instanceof DbInterface) {
            throw new DbException('connection is not DbInterface', 1000);
        }
    }

    private function checkConnection() : void
    {
        if ($this->isRedis) {
            $this->checkRedis();
            return;
        }

        $this->checkDatabase();
    }

    /**
     * @description connect to server
     *
     * @return bool
     */
    public function connect() : bool
    {
        $this->checkConnection();
        return $this->connection->connect();
    }

    /**
     * @description get error
     *
     * @return string
     */
    public function getError() : string
    {
        $this->checkConnection();
        return $this->connection->getError();
    }

    /**
     * @description query
     *
     * @param string $sql
     *
     * @return mixed
     */
    public function query(string $sql) : Array
    {
        $this->checkConnection();
        return $this->connection->query($sql);
    }

    /**
     * @description commit transation
     *
     * @return bool
     */
    public function commit() : bool
    {
        $this->checkConnection();
        return $this->connection->commit();
    }

    /**
     * @description open transation
     *
     * @return bool
     */
    public function beginTransaction() : bool
    {
        $this->checkConnection();
        return $this->connection->beginTransaction();
    }

    /**
     * @description cancel transation
     *
     * @return bool
     */
    public function rollBack() : bool
    {
        $this->checkConnection();
        return $this->connection->rollBack();
    }

    /**
     * @description fetch row
     *
     * @param string $table
     *
     * @param Array $condition
     *
     * @param Array $columns
     *
     * @param string $forUpdateType
     *
     * @return Array | bool
     *
     * @throws Exception
     */
    public function fetchRow(string $table, Array | Where $condition, Array $columns = array(), string $forUpdateType = Type::FOR_UPDATE_NO) : Array | bool
    {
        $this->checkConnection();
        return $this->connection->fetchRow($table, $condition, $columns, $forUpdateType);
    }

    /**
     * @description fetch all rows
     *
     * @param string $table
     *
     * @param Array $condition
     *
     * @param Array $columns
     *
     * @return Array
     *
     * @throws Exception
     */
    public function fetchAll(string $table, Array | Where $condition, Array $columns = array()) : array
    {
        $this->checkConnection();
        return $this->connection->fetchAll($table, $condition, $columns);
    }

    /**
     * @description execute update sql
     *
     * @param Update $update
     *
     * @return int
     */
    public function update(Update $update) : int
    {
        $this->checkConnection();
        return $this->connection->update($update);
    }

    /**
     * @description execute insert sql
     *
     * @param Insert $insert
     *
     * @return int
     */
    public function insert(Insert $insert) : int
    {
        $this->checkConnection();
        return $this->connection->insert($insert);
    }

    /**
     * @description execute select sql
     *
     * @param Select $select
     *
     * @param int $type
     *
     * @return Array | bool
     */
    public function select(Select $select, int $type = Select::ALL) : Array | bool
    {
        $this->checkConnection();
        return $this->connection->select($select, $type);
    }

    /**
     * @description batch insert
     *
     * @param BatchInsert $batchInsert
     *
     * @return int
     *
     * @throws DbException
     *
     */
    public function batchInsert(BatchInsert $batchInsert) : int
    {
        $this->checkConnection();
        return $this->connection->batchInsert($batchInsert);
    }

    /**
     * @description 删除
     *
     * @param Delete $delete
     *
     * @return bool
     *
     * @throws Exception
     */
    public function delete(Delete $delete) : int
    {
        $this->checkConnection();
        return $this->connection->delete($delete);
    }

    /**
     * @description run transation
     *
     * @param callable $fun
     *
     * @param mixed $finally
     *
     * @param ...$params
     *
     * @return bool
     *
     * @throws DbException
     */
    public function transaction(callable $fun, $finally, ...$params) : bool
    {
        $this->checkConnection();
        return $this->connection->transaction($fun, $finally, ...$params);
    }

    /**
     * @description exec sql
     *
     * @param string $sql
     *
     * @return int
     *
     * @throws DbException
     */
    public function exec(string $sql = '') : int
    {
        $this->checkConnection();
        if (!$this->isRedis) {
            return $this->connection->exec($sql);
        }

        $result = $this->connection->exec();
        if (empty($result)) {
            return 0;
        }

        return count($result);
    }

    /**
     * @description is in transation
     *
     * @return bool
     */
    public function inTransaction() : bool
    {
        $this->checkConnection();
        return $this->connection->inTransaction();
    }

    /**
     * @description get last insert id
     *
     * @return int
     */
    public function getLastInsertId() : int
    {
        $this->checkConnection();
        return $this->connection->getLastInsertId();
    }

    /**
     * @description check redis
     */
    private function checkRedis() : void
    {
        $this->initConnection();
        if (!$this->connection instanceof RedisInterface) {
            throw new DbException('connection is not instanceof RedisInterface', 1001);
        }
    }

    /**
     * @description call redis method
     *
     * @param string $method
     *
     * @param Array $params
     *
     * @return mixed
     */
    public function __call(string $method, Array $params) : mixed
    {
        $this->checkConnection();
        return $this->connection->$method(...$params);
    }

    /**
     * @description collect connection
     *
     * @return void
     */
    public function collect() : void
    {
        if ($this->isCollected) {
            return;
        }

        if (empty($this->pool)
            || empty($this->connection)
        ) {
            return;
        }

        try {
            if ($this->connection instanceof DbInterface) {
                if ($this->connection->inTransaction()) {
                    $this->connection->rollBack();
                }
            }
        } catch (\Throwable $e) {
        }

        $this->pool->put($this->connection);
        $this->isCollected = true;
    }

    /**
     * @description destruct
     *
     * @return null
     */
    public function __destruct()
    {
        $this->collect();
    }

    public function setTraceId(string $traceId) : void
    {
        $this->traceId = $traceId;
    }

    public function setSpanId(string $spanId) : void
    {
        $this->spanId = $spanId;
    }

    public function getTraceId() : string
    {
        return $this->traceId;
    }

    public function getSpanId() : string
    {
        return $this->spanId;
    }
}
