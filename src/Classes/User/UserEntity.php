<?php

namespace Classes\User;

use http\Exception\InvalidArgumentException;
use Services\ValidationService;

class UserEntity
{
    private int $id;
    private string $email;
    private string $password;
    private int $age;
    private string $gender;
    private string $phone;
    private string $created_at;
    private string $updated_at;
    private ValidationService $validation_service;
    public const string DATE_FORMAT = 'Y-m-d';

    public function __construct()
    {
        $this->validation_service = new ValidationService();
    }

    public function setId(int $id): void
    {
        if (!$this->validation_service->isPositive($id)) {
            throw new InvalidArgumentException('ID must be a positive integer.');
        }

        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
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
        $this->password = $password;
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

        if (!$this->validation_service->isEmail($this->email)) {
            $errors['email'] = 'Email is invalid.';
        }

        if (!$this->validation_service->isValidPassword($this->password)) {
            $errors['password'] = 'Password is invalid.';
        }

        if (!$this->validation_service->isPositiveInt($this->age)) {
            $errors['age'] = 'Age must be a positive integer.';
        }

        if (!$this->validation_service->isGender($this->gender)) {
            $errors['gender'] = 'Gender is invalid.';
        }

        if (!$this->validation_service->isPhoneNumber($this->phone)) {
            $errors['phone'] = 'Phone number is invalid.';
        }

        if (!$this->validation_service->isDate($this->created_at, self::DATE_FORMAT)) {
            $errors['created_at'] = 'Created at is invalid.';
        }

        if (!$this->validation_service->isDate($this->updated_at, self::DATE_FORMAT)) {
            $errors['updated_at'] = 'Updated at is invalid.';
        }

        return $errors;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
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
