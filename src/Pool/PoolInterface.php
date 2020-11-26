<?php
/**
 *
 * @description 连接池接口
 * 连接池
 * swoole中，在work中使用连接池，每个进程是不共享的
 * 因为协程的channel不共享
 *
 * @package     Pool
 *
 * @time        Tue Sep 24 09:06:42 2019
 *
 * @author      kovey
 */
namespace Kovey\Connection\Pool;

use Kovey\Db\DbInterface;
use Kovey\Redis\RedisInterface;

interface PoolInterface
{
    /**
     * @description 初始化连接池
     *
     * @return null
     */
    public function init();

    /**
     * @description 检测连接池是否为空
     *
     * @return bool
     */
    public function isEmpty() : bool;

    /**
     * @description 放回连接池
     *
     * @return null
     */
    public function put(DbInterface | RedisInterface $db);

    /**
     * @description 从连接池中获取连接
     *
     * @return DbInterface | RedisInterface
     */
    public function pop() : DbInterface | RedisInterface | bool;

    /**
     * @description 获取错误
     *
     * @return Array
     */
    public function getErrors() : Array;

    /**
     * @description 获取链接池写名称
     *
     * @return string
     */
    public static function getWriteName() : string;
 
    /**
     * @description 获取链接池读名称
     *
     * @return string
     */
    public static function getReadName() : string;
}
