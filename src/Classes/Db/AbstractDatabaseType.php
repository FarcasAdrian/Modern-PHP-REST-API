<?php

declare(strict_types=1);

namespace Classes\Db;

use Interfaces\DatabaseTypeInterface;
use Traits\SingletonTrait;

abstract class AbstractDatabaseType implements DatabaseTypeInterface
{
    use SingletonTrait;
    protected mixed $databaseInstance = null;

    protected function __construct() {}

    public function __destruct()
    {
        $this->closeConnection();
    }

    public function getDatabaseInstance(): mixed
    {
        if ($this->databaseInstance === null) {
            $this->connect();
        }

        return $this->databaseInstance;
    }

    public abstract static function getInstance(): mixed;
    abstract protected function closeConnection(): void;
    abstract protected function connect(): void;
}
