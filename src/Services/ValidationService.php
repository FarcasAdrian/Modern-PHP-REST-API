<?php

namespace Services;

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
        if (empty($gender)) {
            return false;
        }

        return true;
    }
}
