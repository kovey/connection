<?php
/**
 * @description Redis pool
 *
 * @package
 *
 * @author zhayai
 *
 * @time 2020-05-09 14:45:07
 *
 */
namespace Kovey\Connection\Pool;

use Kovey\Redis\Redis\Redis as RDS;
use Kovey\Db\DbInterface;
use Kovey\Redis\RedisInterface;

class Redis extends Base
{
    /**
     * @description pool name
     *
     * @var string
     */
    const POOL_NAME = 'pool_redis';

    
    /**
     * @description pool name write
     */
    const POOL_NAME_WRITE = 'pool_redis_write';

    /**
     * @description pool name read
     */
    const POOL_NAME_READ = 'pool_redis_read';

    /**
     * @description init connection
     *
     * @return DbInterface | RedisInterface
     */
    protected function initConnection() : DbInterface | RedisInterface | bool
    {
        $redis = new RDS($this->conf);
        if (!$redis->connect()) {
            $this->errors[] = $redis->getError();
            return false;
        }

        return $redis;
    }
    
    /**
     * @description get write name
     *
     * @return string
     */
    public static function getWriteName() : string
    {
        return self::POOL_NAME_WRITE;
    }

    /**
     * @description get read name
     *
     * @return string
     */
    public static function getReadName() : string
    {
        return self::POOL_NAME_READ;
    }
}
