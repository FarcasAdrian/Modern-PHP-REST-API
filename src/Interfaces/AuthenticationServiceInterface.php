<?php

declare(strict_types=1);

namespace Interfaces;

use stdClass;

interface AuthenticationServiceInterface
{
    public function authenticate(array $data): ?stdClass;
}
