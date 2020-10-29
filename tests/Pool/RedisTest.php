<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2020-10-29 15:34:24
 *
 */
namespace Kovey\Connection\Pool;

use PHPUnit\Framework\TestCase;
use Kovey\Redis\RedisInterface;

class RedisTest extends TestCase
{
    public function testPool()
    {
        $pool = new Redis(array(
            'min' => 2,
            'max' => 4
        ), array(
            'host' => '127.0.0.1',
            'port' => 6379,
            'db' => 0
        ));

        $pool->init();
        $this->assertEquals(array(), $pool->getErrors());
        $this->assertFalse($pool->isEmpty());
        $this->assertInstanceOf(RedisInterface::class, $pool->pop());
        $this->assertInstanceOf(RedisInterface::class, $pool->pop());
        $this->assertInstanceOf(RedisInterface::class, $pool->pop());
        $this->assertInstanceOf(RedisInterface::class, $pool->pop());
        $this->assertFalse($pool->pop());
        $this->assertEquals('pool_redis_read', $pool::getReadName());
        $this->assertEquals('pool_redis_write', $pool::getWriteName());
    }
}
