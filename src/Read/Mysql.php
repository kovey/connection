<?php
/**
 * @description mysql read pool
 *
 * @package
 *
 * @author kovey
 *
 * @time 2020-04-20 16:42:01
 *
 */
namespace Kovey\Connection\Read;

use Kovey\Pool\Mysql as MQ;
use Kovey\Connection\Pool;

class Mysql extends Pool
{
    /**
     * @description construct
     *
     * @param mixed $app
     *
     * @param int $partition
     *
     * @return Mysql
     */
    public function __construct($app, $partition = 0)
    {
        parent::__construct($app->getPool(MQ::getReadName(), $partition));
    }
}
