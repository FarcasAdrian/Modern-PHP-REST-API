<?php

declare(strict_types=1);

namespace Classes;

use Interfaces\ConfigInterface;
use Traits\SingletonTrait;

final class Config implements ConfigInterface
{
    use SingletonTrait;
    private static array $values = [];

    private function __construct()
    {
        self::$values = $_ENV;
    }

    public static function getValue(int|string $key): mixed
    {
        self::getInstance();

        return self::$values[$key] ?? null;
    }

    public static function setValue(string $key, mixed $value): void
    {
        self::getInstance();

        self::$values[$key] = $value;
    }
}
