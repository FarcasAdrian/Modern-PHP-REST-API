<?php

declare(strict_types=1);

namespace Interfaces;

interface DataBagInterface
{
    public function getAll();
    public function get(string $key): string;
    public function add(string $key, string $value): void;
    public function existsKey(string $key): bool;
}
