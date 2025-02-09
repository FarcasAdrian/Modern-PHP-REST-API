<?php

declare(strict_types=1);

namespace Interfaces;

interface UserServiceInterface
{
    public function createPasswordHash(string $password): string;
    public function verifyPassword(string $entered_password, string $hash_password): bool;
}
