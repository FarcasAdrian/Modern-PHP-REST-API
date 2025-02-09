<?php

declare(strict_types=1);

namespace Interfaces;

interface RepositoryInterface
{
    public function getEntityName(): string;
    public function getById(int $id): array;
    public function create(EntityInterface $entity): array;
    public function update(int $id, EntityInterface $entity): ?array;
    public function delete(int $id): bool;
    public function getAll(): array;
    public function findBy(array $data): array;
}
