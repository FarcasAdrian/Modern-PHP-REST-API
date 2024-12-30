<?php

namespace Classes\User;

use http\Exception\InvalidArgumentException;
use Services\ValidationService;
use Services\UserService;

class UserEntity
{
    private int $id;
    private string $name;
    private string $email;
    private string $password;
    private int $age;
    private string $gender;
    private string $phone;
    private string $created_at;
    private string $updated_at;
    private ValidationService $validation_service;
    private UserService $user_service;
    public const DATE_FORMAT = 'Y-m-d H:i:s';

    public function __construct(ValidationService $validation_service, UserService $user_service)
    {
        $this->validation_service = $validation_service;
        $this->user_service = $user_service;
    }

    public function setId(int $id): void
    {
        if (!$this->validation_service->isPositive($id)) {
            throw new InvalidArgumentException('ID must be a positive integer.');
        }

        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id ?? null;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $this->user_service->createPasswordHash($password);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setUpdatedAt(string $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

    public function validate(): array
    {
        $errors = [];

        if (!$this->validation_service->isNotEmptyString($this->getName())) {
            $errors['name'] = 'Name is invalid.';
        }

        if (!$this->validation_service->isEmail($this->getEmail())) {
            $errors['email'] = 'Email is invalid.';
        }

        if (!$this->validation_service->isValidPassword($this->getPassword())) {
            $errors['password'] = 'Password is invalid.';
        }

        if (!$this->validation_service->isPositiveInt($this->getAge())) {
            $errors['age'] = 'Age must be a positive integer.';
        }

        if (!$this->validation_service->isGender($this->getGender())) {
            $errors['gender'] = 'Gender is invalid.';
        }

        if (!$this->validation_service->isPhoneNumber($this->getPhone())) {
            $errors['phone'] = 'Phone number is invalid.';
        }

        if (!$this->validation_service->isDate($this->getCreatedAt(), self::DATE_FORMAT)) {
            $errors['created_at'] = 'Created at is invalid.';
        }

        if (!$this->validation_service->isDate($this->getUpdatedAt(), self::DATE_FORMAT)) {
            $errors['updated_at'] = 'Updated at is invalid.';
        }

        return $errors;
    }

    public function populateFromArray(array $data): self
    {
        if (isset($data['id'])) {
            $this->setId($data['id']);
        }

        $this->setName($data['name'] ?? '');
        $this->setEmail($data['email'] ?? '');
        $this->setPassword($data['password'] ?? '');
        $this->setAge($data['age'] ?? 0);
        $this->setGender($data['gender'] ?? '');
        $this->setPhone($data['phone'] ?? '');
        $this->setCreatedAt($data['created_at'] ?? '');
        $this->setUpdatedAt($data['updated_at'] ?? '');

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'password' => $this->getPassword(),
            'age' => $this->getAge(),
            'gender' => $this->getGender(),
            'phone' => $this->getPhone(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt(),
        ];
    }
}
