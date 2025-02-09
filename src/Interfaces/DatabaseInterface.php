<?php

declare(strict_types=1);

namespace Interfaces;

interface DatabaseInterface
{
    public function executeQuery(string $query, string $types = '', ...$params): mixed;
    public function getRow($statement): array;
    public function getAll($statement): array;
    public function getInsertId($statement): mixed;
    public function getTotalAffectedRows($statement): int;
}
