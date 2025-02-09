<?php

declare(strict_types=1);

namespace Classes;

use Exception;

class App
{
    private static ?object $container = null;

    public static function setContainer(object $container): void
    {
        self::$container = $container;
    }

    public static function getContainer(): ?object
    {
        if (self::$container === null) {
            throw new Exception('Container is not set.');
        }

        return self::$container;
    }

    public static function bind(string $key, object $resolver): void
    {
        self::getContainer()->bind($key, $resolver);
    }

    public static function resolve(string $key): mixed
    {
        return self::getContainer()->resolve($key);
    }
}
