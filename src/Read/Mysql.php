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

use Kovey\Connection\Pool\Mysql as MQ;
use Kovey\Connection\Pool;
use Kovey\Connection\AppInterface;

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
    public function __construct(AppInterface $app, int $partition = 0)
    {
        parent::__construct($app->getPool(MQ::getReadName(), $partition));
    }
}
