<?php

declare(strict_types=1);

namespace Interfaces;

interface RouterInterface
{
    public function parseRoute(string $url = ''): string;
}
