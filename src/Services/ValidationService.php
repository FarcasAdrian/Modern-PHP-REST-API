<?php

declare(strict_types=1);

namespace Services;

use Classes\User\UserEntity;
use Enums\DateEnum;
use DateTime;

class ValidationService
{
    public function isNotEmptyString(?string $string): bool
    {
        return !empty($string);
    }

    public function isEmail(?string $email): bool
    {
        if (empty($email)) {
            return false;
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function isPositive(int|float $number): bool
    {
        return $number > 0;
    }

    public function isPositiveInt(mixed $number): bool
    {
        return is_int($number) && $number > 0;
    }

    public function isPhoneNumber(?string $phone_number): bool
    {
        if (empty($phone_number)) {
            return false;
        }

        return strlen($phone_number) >= 10;
    }

    public function isDate(?string $date, string $format): bool
    {
        if (empty($date)) {
            return false;
        }

        $date_time = DateTime::createFromFormat($format, $date);

        return $date_time && $date_time->format($format) === $date;
    }

    public function isValidPassword(?string $password): bool
    {
        if (empty($password)) {
            return false;
        }

        return true;
    }

    public function isGender(?string $gender): bool
    {
        return !empty($gender);
    }

    public function isValidAuthHeader(string $auth_header, array &$matches): bool
    {
        return $auth_header && preg_match('/Bearer\s(\S+)/', $auth_header, $matches);
    }

    public function validateUser(UserEntity $userEntity): array
    {
        $errors = [];

        if (!$this->isNotEmptyString($userEntity->getName())) {
            $errors['name'] = 'Name is invalid.';
        }

        if (!$this->isEmail($userEntity->getEmail())) {
            $errors['email'] = 'Email is invalid.';
        }

        if (!$this->isValidPassword($userEntity->getPassword())) {
            $errors['password'] = 'Password is invalid.';
        }

        if (!$this->isPositiveInt($userEntity->getAge())) {
            $errors['age'] = 'Age must be a positive integer.';
        }

        if (!$this->isGender($userEntity->getGender())) {
            $errors['gender'] = 'Gender is invalid.';
        }

        if (!$this->isPhoneNumber($userEntity->getPhone())) {
            $errors['phone'] = 'Phone number is invalid.';
        }

        if (!$this->isDate($userEntity->getCreatedAt(), DateEnum::DATE_FORMAT->value)) {
            $errors['created_at'] = 'Created at is invalid.';
        }

        if (!$this->isDate($userEntity->getUpdatedAt(), DateEnum::DATE_FORMAT->value)) {
            $errors['updated_at'] = 'Updated at is invalid.';
        }

        return $errors;
    }
}
