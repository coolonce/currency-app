<?php

namespace App\Interfaces;

interface CacheInterface
{
    public function get(string $key);
    public function set(string $key, $value, int $ttl = 3600);
    public function exists(string $key): int;
    public function delete(string $key): bool;
    public function clear(): bool;
}