<?php

declare(strict_types=1);

namespace Handlers;

use Interfaces\RedisHandlerInterface;
use Predis\Client;

class RedisHandler implements RedisHandlerInterface
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

    public function set(string $key, mixed $value, int $expire_time): void
    {
        $this->client->set($key, $value, $expire_time);
    }

    public function get(string $key): ?string
    {
        return $this->client->get($key);
    }

    public function delete(string $key): int
    {
        return $this->client->del([$key]);
    }

    public function exists(string $key): int
    {
        return $this->client->exists($key);
    }
}
