<?php

declare(strict_types=1);

namespace Transformers;

use Classes\User\UserEntity;
use Interfaces\EntityTransformerInterface;
use Interfaces\UserServiceInterface;

class UserEntityTransformer implements EntityTransformerInterface
{
    public function __construct(private UserServiceInterface $userService) {}

    public function toArray($userEntity): array
    {
        return [
            'id' => $userEntity->getId(),
            'name' => $userEntity->getName(),
            'email' => $userEntity->getEmail(),
            'password' => $userEntity->getPassword(),
            'age' => $userEntity->getAge(),
            'gender' => $userEntity->getGender(),
            'phone' => $userEntity->getPhone(),
            'created_at' => $userEntity->getCreatedAt(),
            'updated_at' => $userEntity->getUpdatedAt(),
        ];
    }

    public function populateFromArray($userEntity, array $data): UserEntity
    {
        if (isset($data['id'])) {
            $userEntity->setId($data['id']);
        }

        $password = $data['password'] ? $this->userService->createPasswordHash($data['password']) : '';

        $userEntity->setName($data['name'] ?? '');
        $userEntity->setEmail($data['email'] ?? '');
        $userEntity->setPassword($password);
        $userEntity->setAge($data['age'] ?? 0);
        $userEntity->setGender($data['gender'] ?? '');
        $userEntity->setPhone($data['phone'] ?? '');
        $userEntity->setCreatedAt($data['created_at'] ?? '');
        $userEntity->setUpdatedAt($data['updated_at'] ?? '');

        return $userEntity;
    }
}
