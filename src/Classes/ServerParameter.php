<?php

declare(strict_types=1);

namespace Classes;

use Interfaces\ServerParameterInterface;

class ServerParameter implements ServerParameterInterface
{
    private ?array $server_parameters;

    public function __construct()
    {
        $this->initServerParameters();
    }

    public function getAll()
    {
        return $this->server_parameters ?? [];
    }

    public function get(string $key): string
    {
        return $this->server_parameters[$key] ?? '';
    }

    public function add(string $key, string $value): void
    {
        $this->server_parameters[$key] = $value;
    }

    public function existsKey(string $key): bool
    {
        return isset($this->server_parameters[$key]);
    }

    /**
     * Set parameters from $_POST.
     * @return void
     */
    private function initServerParameters(): void
    {
        $server_parameters = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (empty($server_parameters)) {
            $raw_data = file_get_contents('php://input');
            $server_parameters = json_decode($raw_data, true);
        }

        $this->server_parameters = is_array($server_parameters) ? $server_parameters : [];
    }
}
