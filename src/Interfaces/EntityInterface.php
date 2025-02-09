<?php

declare(strict_types=1);

namespace Interfaces;

interface EntityInterface
{
    public function getId(): ?int;
    public function setId(int $id): void;
}
