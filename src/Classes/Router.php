<?php

declare(strict_types=1);

namespace Classes;

use Interfaces\RouterInterface;

class Router implements RouterInterface
{
    public function __construct(private string $url) {}

    public function parseRoute(string $url = ''): string
    {
        if (empty($url)) {
            $url = $this->url;
        }

        $request_uri = explode('/api/', $url);
        $endpoint = explode('?', end($request_uri));

        return is_array($endpoint) ? array_shift($endpoint) : '';
    }
}
