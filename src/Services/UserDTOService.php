<?php

declare(strict_types=1);

namespace Services;

use Classes\User\UserEntity;
use DTO\UserDTO;
use Interfaces\DTOInterface;
use Interfaces\EntityDTOServiceInterface;
use Interfaces\EntityInterface;

class UserDTOService implements EntityDTOServiceInterface
{
    /**
     * @param UserEntity $entity
     * @return UserDTO
     */
    public function createDTOFromEntity(EntityInterface $entity): UserDTO
    {
        return new UserDTO(
            $entity->getId(),
            $entity->getName(),
            $entity->getEmail(),
            $entity->getAge(),
            $entity->getGender(),
            $entity->getPhone(),
            $entity->getCreatedAt(),
            $entity->getUpdatedAt()
        );
    }

    /**
     * @param array $userData
     * @return UserDTO
     */
    public function createDTOFromArray(array $userData): UserDTO
    {
        return new UserDTO(
            $userData['id'],
            $userData['name'],
            $userData['email'],
            $userData['age'],
            $userData['gender'],
            $userData['phone'],
            $userData['created_at'],
            $userData['updated_at']
        );
    }

    /**
     * @param UserDTO $userDTO
     * @param UserEntity $userEntity
     * @return UserEntity
     */
    public function populateEntityFromDTO(DTOInterface $userDTO, EntityInterface $userEntity): UserEntity
    {
        $userEntity->setId($userDTO->id);
        $userEntity->setName($userDTO->name);
        $userEntity->setEmail($userDTO->email);
        $userEntity->setAge($userDTO->age);
        $userEntity->setGender($userDTO->gender);
        $userEntity->setPhone($userDTO->phone);
        $userEntity->setCreatedAt($userDTO->created_at);
        $userEntity->setUpdatedAt($userDTO->updated_at);

        return $userEntity;
    }
}
