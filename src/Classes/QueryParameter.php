<?php

declare(strict_types=1);

namespace Classes;

use Interfaces\QueryParameterInterface;

class QueryParameter implements QueryParameterInterface
{
    private ?array $query_parameters;

    public function __construct()
    {
        $this->initQueryParameters();
    }

    public function getAll()
    {
        return $this->query_parameters;
    }
    public function get(string $key): string
    {
        return $this->query_parameters[$key] ?? '';
    }

    public function add(string $key, string $value): void
    {
        $this->query_parameters[$key] = $value;
    }

    public function existsKey(string $key): bool
    {
        return isset($this->query_parameters[$key]);
    }

    private function initQueryParameters(): void
    {
        $query_parameters = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $this->query_parameters = is_array($query_parameters) ? $query_parameters : [];
    }
}
