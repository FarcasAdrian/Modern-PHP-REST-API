<?php 

declare(strict_types=1);

namespace Controllers;

class Controller
{
    public function execute(mixed $method): mixed
    {
        if (method_exists($this, $method)) {
            return $method();
        }

        return null;
    }
}
