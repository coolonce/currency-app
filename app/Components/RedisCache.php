<?php

namespace App\Components;

use App\Interfaces\CacheInterface;
use Predis\Client as RedisClient;

class RedisCache implements CacheInterface
{
    protected RedisClient $redis;

    public function __construct()
    {
        $this->redis = new RedisClient([
            'scheme' => 'tcp',
            'host'   => getenv('REDIS_HOST'),
            'port'   => getenv('REDIS_PORT'),
        ]);

        $this->redis->connect();
    }

    public function get(string $key)
    {
        return $this->redis->get($key);
    }

    public function set(string $key, $value, int $ttl = 3600)
    {
        return $this->redis->setex($key, $ttl, $value);
    }

    public function exists(string $key): int
    {
        return $this->redis->exists($key);
    }

    public function delete(string $key): bool
    {
        return $this->redis->del($key) > 0;
    }

    public function clear(): bool
    {
        return $this->redis->flushDB();
    }
}