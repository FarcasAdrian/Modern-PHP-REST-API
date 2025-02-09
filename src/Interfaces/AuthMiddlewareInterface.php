<?php

declare(strict_types=1);

namespace Interfaces;

use Classes\Request;
use stdClass;

interface AuthMiddlewareInterface
{
    public function handle(Request $request): ?stdClass;
}
