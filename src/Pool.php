<?php
/**
 * @description 连接池
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
use Kovey\Db\DbInterface;
use Kovey\Redis\RedisInterface;
use Kovey\Db\Exception\DbException;

class Pool
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
     * @description construct
     *
     * @param PortInterface $pool
     *
     * @return Pool
     */
    public function __construct(PoolInterface $pool)
    {
        $this->pool = $pool;
        $this->connection = $this->pool->pop();
        if (empty($this->connection)) {
            throw new DbException('pool is empty', 1012);
        }
    }

    /**
     * @description connection
     *
     * @return RedisInterface | DbInterface
     */
    public function getConnection() : RedisInterface | DbInterface
    {
        return $this->connection;
    }

    /**
     * @description destruct
     *
     * @return null
     */
    public function __destruct()
    {
        if (empty($this->pool)
            || empty($this->connection)
        ) {
            return;
        }

        $this->pool->put($this->connection);
    }
}
