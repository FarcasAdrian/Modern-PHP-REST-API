<?php

declare(strict_types=1);

namespace Traits;

trait EntityIdTrait
{
    private ?int $id;

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
