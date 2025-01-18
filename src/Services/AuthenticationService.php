<?php

declare(strict_types=1);

namespace Services;

use Classes\Response;
use Classes\User\User;
use Firebase\JWT\JWT;
use Exception;
use Firebase\JWT\Key;
use stdClass;
use Enums\HttpStatusCodeEnum;

class AuthenticationService
{
    public function __construct(private User $user, private Response $response, private UserService $user_service) {}

    /**
     * @param string $user_email
     * @param string $user_password
     * @return string|null
     */
    public function authenticateUser(string $user_email, string $user_password): ?string
    {
        try {
            $data = ['email' => $user_email];
            $user_data = $this->user->findBy($data);

            if (empty($user_data) || !$this->user_service->verifyPassword($user_password, $user_data['password'])) {
                $this->response->sendResponse(HttpStatusCodeEnum::UNAUTHORIZED_STATUS_CODE->value, 'Authentication failed.');
                return null;
            }
        } catch (Exception $exception) {
            $this->response->sendResponse(
                HttpStatusCodeEnum::UNAUTHORIZED_STATUS_CODE->value,
                'Something went wrong during authentication: ' . $exception->getMessage()
            );
            return null;
        }

        try {
            $issued_at = time();
            $expiration_time = $issued_at + (int) $_ENV['AUTHENTICATION_EXPIRATION_TIME'];
            $payload = [
                'iss' => $_ENV['WEBSITE_URL'],
                'aud' => $_ENV['WEBSITE_URL'],
                'iat' => $issued_at,
                'exp' => $expiration_time,
                'data' => [
                    'username' => $user_email,
                ]
            ];

            return JWT::encode($payload, $_ENV['JWT_SECRET_KEY'], 'HS256');
        } catch (Exception $exception) {
            $this->response->sendResponse(
                HttpStatusCodeEnum::INTERNAL_SERVER_ERROR_STATUS_CODE->value,
                'JWT encoding failed: ' . $exception->getMessage()
            );
            return null;
        }
    }

    /**
     * @param string $jwt
     * @return stdClass|null
     */
    public function decodeToken(string $jwt): ?stdClass
    {
        try {
            return JWT::decode($jwt, new Key($_ENV['JWT_SECRET_KEY'], 'HS256'));
        } catch (Exception $exception) {
            $this->response->sendResponse(
                HttpStatusCodeEnum::UNAUTHORIZED_STATUS_CODE->value,
                'Invalid token: ' . $exception->getMessage()
            );
            return null;
        }
    }
}
