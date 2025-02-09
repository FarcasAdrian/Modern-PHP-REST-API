<?php

declare(strict_types=1);

namespace Services;

use ReflectionClass;
use Closure;
use Interfaces\ServiceContainerInterface;

class ServiceContainer implements ServiceContainerInterface
{
    private array $bindings = [];

    public function bind(string $key, Closure $resolver): void
    {
        $this->bindings[$key] = $resolver;
    }

    public function resolve(string $key): mixed
    {
        if (array_key_exists($key, $this->bindings)) {
            return call_user_func($this->bindings[$key]);
        }

        $reflector = new ReflectionClass($key);
        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            return new $key;
        }

        $constructor_parameters = $constructor->getParameters();
        $dependencies = [];

        foreach ($constructor_parameters as $parameter) {
            $type = (string) $parameter->getType();
            $dependencies[] = $this->resolve($type);
        }

        return $reflector->newInstanceArgs($dependencies);
    }
}
