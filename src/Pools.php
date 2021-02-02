<?php
/**
 * @description
 *
 * @package
 *
 * @author kovey
 *
 * @time 2021-02-02 11:15:58
 *
 */
namespace Kovey\Connection;

use Kovey\Connection\Pool\PoolInterface;
use Kovey\Logger\Logger;

class Pools
{
    /**
     * @description pools
     *
     * @var Array
     */
    private Array $pools;

    public function __construct()
    {
        $this->pools = array();
    }

    public function get(string $name, int $partition) : ?PoolInterface
    {
        return $this->pools[$name][$partition] ?? null;
    }

    public function add(string $name, PoolInterface $pool, int $partition = 0) : Pools
    {
        $this->pools[$name] ??= array();
        $this->pools[$name][$partition] = $pool;
        return $this;
    }

    public function init() : Pools
    {
        try {
            foreach ($this->pools as $pool) {
                if (is_array($pool)) {
                    foreach ($pool as $pl) {
                        $pl->init();
                        if (count($pl->getErrors()) > 0) {
                            Logger::writeErrorLog(__LINE__, __FILE__, implode(';', $pl->getErrors()));
                        }
                    }
                    continue;
                }

                $pool->init();
                if (count($pool->getErrors()) > 0) {
                    Logger::writeErrorLog(__LINE__, __FILE__, implode(';', $pool->getErrors()));
                }
            }
        } catch (\Throwable $e) {
            Logger::writeExceptionLog(__LINE__, __FILE__, $e);
        }

        return $this;
    }
}
