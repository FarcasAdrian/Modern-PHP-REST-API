<?php

namespace Classes;

use Predis\Client;

class RedisHandler
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'scheme' => $_ENV['REDIS_SCHEME'],
            'host' => $_ENV['REDIS_HOST'],
            'port' => $_ENV['REDIS_PORT'],
            'password' => $_ENV['REDIS_PASSWORD'],
            'database' => $_ENV['REDIS_DATABASE'],
            'timeout' => $_ENV['REDIS_TIMEOUT'],
            'persistent' => $_ENV['REDIS_PERSISTENT'],
        ]);
    }

    public function set(string $key, mixed $value): void
    {
        $this->client->set($key, $value);
    }

    public function get(string $key): ?string
    {
        return $this->client->get($key);
    }

    public function delete(string $key): int
    {
        return $this->client->del([$key]);
    }

    public function exists(string $key): bool
    {
        return $this->client->exists($key);
    }
}
