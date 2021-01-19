<?php
/**
 * @description mysql pool
 *
 * @package
 *
 * @author kovey
 *
 * @time 2020-05-09 14:41:02
 *
 */
namespace Kovey\Connection\Pool;

use Kovey\Db\DbInterface;
use Kovey\Redis\RedisInterface;
use Kovey\Db\Mysql as MSQ;

class Mysql extends Base
{
    /**
     * @description Pool name
     */
    const POOL_NAME = 'pool_mysql';

    /**
     * @description init connection
     *
     * @return DbInterface | RedisInterface
     */
    protected function initConnection() : DbInterface | RedisInterface | bool
    {
        $db = new MSQ($this->conf);
        if (!$db->connect()) {
            $this->errors[] = $db->getError();
            return false;
        }

        return $db;
    }

    /**
     * @description get write name
     *
     * @return string
     */
    public static function getWriteName() : string
    {
        return self::POOL_NAME . '_write';
    }

    /**
     * @description get read name
     *
     * @return string
     */
    public static function getReadName() : string
    {
        return self::POOL_NAME . '_read';
    }
}
