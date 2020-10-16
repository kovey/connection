<?php
/**
 * @description redis read connection
 *
 * @package
 *
 * @author kovey
 *
 * @time 2020-04-20 16:40:05
 *
 */
namespace Kovey\Connection\Read;

use Kovey\Connection\Pool\Redis as RD;
use Kovey\Connection\Pool;

class Redis extends Pool
{
    /**
     * @description construct
     *
     * @param mixed $app
     *
     * @param int $partition
     *
     * @return Redis
     */
    public function __construct($app, $partition = 0)
    {
        parent::__construct($app->getPool(RD::getReadName(), $partition));
    }
}
