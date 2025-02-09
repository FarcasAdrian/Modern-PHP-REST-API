<?php

declare(strict_types=1);

namespace Interfaces;

interface RedisHandlerInterface
{
    public function set(string $key, mixed $value, int $expire_time): void;
    public function get(string $key): ?string;
    public function delete(string $key): int;
    public function exists(string $key): int;
}
