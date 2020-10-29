<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2020-10-28 18:32:08
 *
 */
namespace Kovey\Connection\Pool;

use PHPUnit\Framework\TestCase;
use Kovey\Db\Adapter;
use Kovey\Db\DbInterface;

class MysqlTest extends TestCase
{
    public function testPool()
    {
        $pool = new Mysql(array(
            'min' => 2,
            'max' => 4
        ), array(
            'dbname' => 'test',
            'host' => '127.0.0.1',
            'username' => 'root',
            'password' => '',
            'port' => 3306,
            'charset' => 'UTF8',
            'adapter' => Adapter::DB_ADAPTER_PDO,
            'options' => array()
        ));

        $pool->init();
        $this->assertEquals(array(), $pool->getErrors());
        $this->assertFalse($pool->isEmpty());
        $this->assertInstanceOf(DbInterface::class, $pool->pop());
        $this->assertInstanceOf(DbInterface::class, $pool->pop());
        $this->assertInstanceOf(DbInterface::class, $pool->pop());
        $this->assertInstanceOf(DbInterface::class, $pool->pop());
        $this->assertFalse($pool->pop());
        $this->assertEquals('pool_mysql_read', $pool::getReadName());
        $this->assertEquals('pool_mysql_write', $pool::getWriteName());
    }
}
