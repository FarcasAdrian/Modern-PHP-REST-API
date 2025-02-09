<?php

declare(strict_types=1);

namespace Interfaces;

use Closure;

interface ServiceContainerInterface
{
    public function bind(string $key, Closure $resolver): void;
    public function resolve(string $key): mixed;
}
