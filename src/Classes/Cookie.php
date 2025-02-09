<?php

declare(strict_types=1);

namespace Classes;

use Interfaces\CookieInterface;

class Cookie implements CookieInterface
{
    private ?array $cookies;

    public function __construct()
    {
        $this->initCookies();
    }

    public function getAll()
    {
        return $this->cookies ?? [];
    }

    public function get(string $key): string
    {
        return $this->cookies[$key] ?? '';
    }

    public function add(string $key, string $value): void
    {
        $this->cookies[$key] = $value;
    }

    public function existsKey(string $key): bool
    {
        return isset($this->cookies[$key]);
    }

    private function initCookies(): void
    {
        $cookies = filter_input_array(INPUT_COOKIE, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $this->cookies = is_array($cookies) ? $cookies : [];
    }
}
