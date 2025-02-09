<?php

declare(strict_types=1);

namespace Interfaces;

interface ConfigInterface
{
    public static function getValue(int|string $key): mixed;
    public static function setValue(string $key, mixed $value): void;
}
