<?php

declare(strict_types=1);

namespace Classes;

class Request
{
    private ?string $request_method;
    private ?array $query_parameters;
    private ?array $server_parameters;
    private ?array $headers;
    private ?array $cookies;

    public function __construct()
    {
        $this->request_method = (string) $_SERVER['REQUEST_METHOD'];
        $this->setQueryParameters();
        $this->setPostParameters();
        $this->setHeaders();
        $this->setCookies();
    }

    /**
     * Return a parameter (GET or POST) using a specific key.
     * @param string $key
     * @return mixed|null
     */
    public function getParameter(string $key): mixed
    {
        if ($this->getRequestMethod() == 'GET') {
            $query_parameters = $this->getQueryParameters();

            return $query_parameters[$key] ?? null;
        }

        $server_parameters = $this->getPostParameters();

        return $server_parameters[$key] ?? null;
    }

    /**
     * Return parameters from $_GET.
     * @return array
     */
    public function getQueryParameters(): array
    {
        return $this->query_parameters ?? [];
    }

    /**
     * Set parameters from $_GET.
     * @return void
     */
    private function setQueryParameters(): void
    {
        $query_parameters = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $this->query_parameters = is_array($query_parameters) ? $query_parameters : [];
    }

    /**
     * Return parameters from $_POST.
     * @return array
     */
    public function getPostParameters(): array
    {
        return $this->server_parameters ?? [];
    }

    /**
     * Set parameters from $_POST.
     * @return void
     */
    private function setPostParameters(): void
    {
        $server_parameters = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (empty($server_parameters)) {
            $raw_data = file_get_contents('php://input');
            $server_parameters = json_decode($raw_data, true);
        }

        $this->server_parameters = is_array($server_parameters) ? $server_parameters : [];
    }

    /**
     * Return the request method.
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->request_method;
    }

    /**
     * Return all headers from $_SERVER.
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers ?? [];
    }

    /**
     * @param string $key
     * @return string
     */
    public function getHeader(string $key): string
    {
        return $this->headers[$key] ?? '';
    }

    /**
     * Set headers from $_SERVER.
     * @return void
     */
    private function setHeaders(): void
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

    /**
     * Return all cookies from $_COOKIE.
     * @return array
     */
    public function getCookies(): array
    {
        return $this->cookies ?? [];
    }

    /**
     * Set cookies from $_COOKIE.
     * @return void
     */
    private function setCookies(): void
    {
        $cookies = filter_input_array(INPUT_COOKIE, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $this->cookies = is_array($cookies) ? $cookies : [];
    }

    /**
     * @return string
     */
    public function getRequestEndpoint(): string
    {
        $request_uri = explode('/api/', $this->getRequestUri());
        $endpoint = explode('?', end($request_uri));

        return is_array($endpoint) ? array_shift($endpoint) : '';
    }

    /**
     * @return string
     */
    public function getRequestUri(): string
    {
        return (string) $_SERVER['REQUEST_URI'];
    }
}
