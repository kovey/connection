<?php
/**
 * @description pool interface
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
     * @description init pool
     *
     * @return void
     */
    public function init() : void;

    /**
     * @description is empty
     *
     * @return bool
     */
    public function isEmpty() : bool;

    /**
     * @description put pool
     *
     * @return void
     */
    public function put(DbInterface | RedisInterface $db) : void;

    /**
     * @description pop pool
     *
     * @return DbInterface | RedisInterface
     */
    public function pop() : DbInterface | RedisInterface | bool;

    /**
     * @description get errors
     *
     * @return Array
     */
    public function getErrors() : Array;

    /**
     * @description get write name
     *
     * @return string
     */
    public static function getWriteName() : string;
 
    /**
     * @description get read name
     *
     * @return string
     */
    public static function getReadName() : string;
}
