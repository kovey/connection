<?php
/**
 * @description pool base
 *
 * @package Connection\Pool
 *
 * @author kovey
 *
 * @time 2020-05-09 14:41:02
 *
 */
namespace Kovey\Connection\Pool;

use Swoole\Coroutine\Channel;
use Kovey\Db\DbInterface;
use Kovey\Redis\RedisInterface;

abstract class Base implements PoolInterface
{
    /**
     * @description pool
     *
     * @var Swoole\Coroutine\Channel
     */
    protected Channel $pool;

    /**
     * @description pool min
     *
     * @var int
     */
    protected int $min;

    /**
     * @description pool max
     *
     * @var int
     */
    protected int $max;

    /**
     * @description pool count
     *
     * @var int
     */
    protected int $count;

    /**
     * @description config
     *
     * @var Array
     */
    protected Array $conf;

    /**
     * @description error
     *
     * @var Array
     */
    protected Array $errors;

    public function __construct(Array $poolConf, Array $conf)
    {
        $this->min = $poolConf['min'] ?? 2;
        $this->max = $poolConf['max'] ?? 10;
        $this->pool = new Channel($this->max);
        $this->conf = $conf;
        $this->errors = array();
        $this->count = 0;
    }

    /**
     * @description init
     *
     * @return void
     */
    public function init() : void
    {
        for ($i = 0; $i < $this->min; $i ++) {
            $db = $this->initConnection();
            if (empty($db)) {
                continue;
            }

            $this->put($db);
            $this->count ++;
        }
    }

    /**
     * @description is empty
     *
     * @return bool
     */
    public function isEmpty() : bool
    {
        return $this->pool->isEmpty();
    }

    /**
     * @description put pool
     *
     * @return void
     */
    public function put(DbInterface | RedisInterface $db) : void
    {
        $this->pool->push($db);
    }

    /**
     * @description pop data
     *
     * @return DbInterface | RedisInterface
     */
    public function pop() : DbInterface | RedisInterface | bool
    {
        $db = $this->pool->pop(1);
        if ($db) {
            return $db;
        }

        if ($this->count >= $this->max) {
            return false;
        }

        $this->errors = array();
        $db = $this->initConnection();
        if (empty($db)) {
            return false;
        }

        $this->count ++;
        return $db;
    }

    /**
     * @description get errors
     *
     * @return Array
     */
    public function getErrors() : Array
    {
        return $this->errors;
    }

    /**
     * @description init connection
     *
     * @return DbInterface | RedisInterface | bool
     */
    abstract protected function initConnection() : DbInterface | RedisInterface | bool;
}
