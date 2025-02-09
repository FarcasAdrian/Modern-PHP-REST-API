<?php

declare(strict_types=1);

namespace Classes;

use Interfaces\HeaderInterface;

class Header implements HeaderInterface
{
    private ?array $headers;

    public function __construct()
    {
        $this->initHeaders();
    }

    public function getAll()
    {
        return $this->headers ?? [];
    }

    public function get(string $key): string
    {
        return $this->headers[$key] ?? '';
    }

    public function add(string $key, string $value): void
    {
        $this->headers[$key] = $value;
    }

    public function existsKey(string $key): bool
    {
        return isset($this->headers[$key]);
    }

    /**
     * Set headers from $_SERVER.
     * @return void
     */
    private function initHeaders(): void
    {
        $this->headers = [];

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                // if we have HTTP_ACCEPT_ENCODING as key, the value from $header is Accept-Encoding
                $header = ucwords(strtolower(substr($key, 5)), '_');
                $header = str_replace('_', '-', $header);
                $this->headers[$header] = $value;
            }
        }

        if (function_exists('apache_request_headers')) {
            $this->headers = array_merge($this->headers, apache_request_headers());
        }
    }
}
