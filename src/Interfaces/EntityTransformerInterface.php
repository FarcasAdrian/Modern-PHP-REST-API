<?php

declare(strict_types=1);

namespace Interfaces;

interface EntityTransformerInterface
{
    public function toArray(EntityInterface $entity): array;
    public function populateFromArray(EntityInterface $entity, array $data): EntityInterface;
}
