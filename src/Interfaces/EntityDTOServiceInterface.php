<?php

declare(strict_types=1);

namespace Interfaces;

interface EntityDTOServiceInterface
{
    public function createDTOFromEntity(EntityInterface $entity): DTOInterface;
    public function createDTOFromArray(array $userData): DTOInterface;
    public function populateEntityFromDTO(DTOInterface $DTO, EntityInterface $entity): EntityInterface;
}
