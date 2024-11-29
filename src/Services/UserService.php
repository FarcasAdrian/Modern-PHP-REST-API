<?php

namespace Services;

class UserService
{
    public function createPasswordHash(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function verifyPassword(string $entered_password, string $hash_password): bool
    {
        return password_verify($entered_password, $hash_password);
    }
}
