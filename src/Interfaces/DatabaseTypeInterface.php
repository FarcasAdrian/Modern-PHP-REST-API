<?php

declare(strict_types=1);

namespace Interfaces;

interface DatabaseTypeInterface
{
    public static function getInstance(): mixed;
    public function getDatabaseInstance(): mixed;
    public function prepare(string $query): ?object;
}
