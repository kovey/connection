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

use Kovey\Connection\PoolInterface;
use Kovey\Db\DbInterface;
use Kovey\Redis\RedisInterface;

class Pool
{
    /**
     * @description pool
     *
     * @var PortInterface
     */
    private PortInterface $pool;

    /**
     * @description connection
     *
     * @var Redis | Mysql
     */
    private RedisInterface | DbInterface $database;

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
        $this->database = $this->pool->getDatabase();
    }

    /**
     * @description connection
     *
     * @return mixed
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * @description destruct
     *
     * @return null
     */
    public function __destruct()
    {
        if (!$this->pool
            || empty($this->database)
        ) {
            return;
        }

        $this->pool->put($this->database);
    }
}
