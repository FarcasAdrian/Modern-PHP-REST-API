<?php

declare(strict_types=1);

namespace Traits;

use Exception;

trait SingletonTrait
{
    protected static ?self $instance = null;

    private function __construct() {}
    private function __destruct() {}
    private function __clone()
    {
        throw new Exception('Cannot clone singleton.');
    }

    public function __wakeup()
    {
        throw new Exception('Cannot deserialize a singleton.');
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
