<?php

declare(strict_types=1);

namespace Classes\Db;

abstract class AbstractTransactionalDatabaseType extends AbstractDatabaseType
{
    abstract public function beginTransaction(): void;
    abstract public function commit(): void;
    abstract public function rollback(): void;
}
