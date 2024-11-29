<?php

namespace Services;

use Classes\Response;
use Classes\User\User;
use Firebase\JWT\JWT;
use Exception;
use Firebase\JWT\Key;
use stdClass;

class AuthenticationService
{
    private User $user;
    private Response $response;
    private UserService $user_service;

    public function __construct(User $user, Response $response, UserService $user_service)
    {
        $this->user = $user;
        $this->response = $response;
        $this->user_service = $user_service;
    }

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
                $this->response->sendResponse(Response::UNAUTHORIZED_STATUS_CODE, 'Authentication failed.');
                return null;
            }
        } catch (Exception $exception) {
            $this->response->sendResponse(
                Response::UNAUTHORIZED_STATUS_CODE,
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
                Response::INTERNAL_SERVER_ERROR_STATUS_CODE,
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
                Response::UNAUTHORIZED_STATUS_CODE,
                'Invalid token: ' . $exception->getMessage()
            );
            return null;
        }
    }
}
