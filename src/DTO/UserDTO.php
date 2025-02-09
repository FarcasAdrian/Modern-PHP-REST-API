<?php

declare(strict_types=1);

namespace DTO;

use Interfaces\DTOInterface;

class UserDTO implements DTOInterface
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public int $age,
        public string $gender,
        public string $phone,
        public string $created_at,
        public string $updated_at
    ) {}
}
