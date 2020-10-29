<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2020-10-28 18:30:33
 *
 */
namespace Kovey\Connection;

use PHPUnit\Framework\TestCase;
use Kovey\Connection\Pool\Mysql;
use Kovey\Db\Adapter;
use Kovey\Db\DbInterface;

class PoolTest extends TestCase
{
    public function testGetItem()
    {
        $pool = new Pool(new Mysql(array(
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
        )));

        $this->assertInstanceOf(DbInterface::class, $pool->getConnection());
    }
}
