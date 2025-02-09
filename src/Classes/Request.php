<?php

declare(strict_types=1);

namespace Classes;

use Interfaces\CookieInterface;
use Interfaces\HeaderInterface;
use Interfaces\QueryParameterInterface;
use Interfaces\ServerParameterInterface;

class Request
{
    private ?string $request_method;

    public function __construct(
        private ServerParameterInterface $serverParameter,
        private QueryParameterInterface $queryParameter,
        private HeaderInterface $header,
        private CookieInterface $cookie
    ) {
        $this->initRequestMethod();
    }

    /**
     * Return a parameter (GET or POST) using a specific key.
     * @param string $key
     * @return mixed|null
     */
    public function getParameter(string $key): mixed
    {
        if ($this->getRequestMethod() == 'GET') {
            $query_parameters = $this->queryParameter->getAll();

            return $query_parameters[$key] ?? null;
        }

        $server_parameters = $this->serverParameter->getAll();

        return $server_parameters[$key] ?? null;
    }

    /**
     * Return the request method.
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->request_method;
    }

    private function initRequestMethod()
    {
        $this->request_method = (string) $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @return string
     */
    public function getRequestUri(): string
    {
        return (string) $_SERVER['REQUEST_URI'];
    }

    public function getServerParameter(): ServerParameterInterface
    {
        return $this->serverParameter;
    }

    public function getQueryParameter(): QueryParameterInterface
    {
        return $this->queryParameter;
    }

    public function getHeader(): HeaderInterface
    {
        return $this->header;
    }

    public function getCookie(): CookieInterface
    {
        return $this->cookie;
    }
}
