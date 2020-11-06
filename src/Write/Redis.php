<?php
/**
 * @description redis write pool
 *
 * @package
 *
 * @author kovey
 *
 * @time 2020-04-20 16:40:05
 *
 */
namespace Kovey\Connection\Write;

use Kovey\Connection\Pool\Redis as RD;
use Kovey\Connection\Pool;
use Kovey\Connection\AppInterface;

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
    public function __construct(AppInterface $app, int $partition = 0)
    {
        parent::__construct($app->getPool(RD::getWriteName(), $partition));
    }
}
