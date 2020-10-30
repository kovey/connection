<?php
/**
 * @description App Interface
 *
 * @package
 *
 * @author kovey
 *
 * @time 2020-10-29 14:27:52
 *
 */
namespace Kovey\Connection;

use Kovey\Connection\Pool\PoolInterface;

Interface AppInterface
{
    /**
     * @description get pool
     *
     * @param string $name
     *
     * @param int $partition
     *
     * @return PoolInterface
     */
    public function getPool(string $name, int $partition = 0) : ?PoolInterface;

    /**
     * @description register pool
     *
     * @param string $name
     *
     * @param PoolInterface $pool
     *
     * @param int $partition
     *
     * @return AppInterface
     *
     */
    public function registerPool(string $name, PoolInterface $pool, int $partition = 0) : AppInterface;
}
    
