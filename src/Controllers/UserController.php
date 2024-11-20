<?php

namespace Controllers;

use Classes\Response;
use Classes\User;
use Exception;

class UserController
{
    private User $user;
    private Response $response;

    public function __construct(User $user, Response $response)
    {
        $this->user = $user;
        $this->response = $response;
    }

    /**
     * Retrieve all available users.
     * @return void
     */
    public function getAll(): void
    {
        $request_method = $this->getRequestMethod();

        if ($request_method !== 'GET' && $request_method !== 'POST') {
            $this->sendResponse(
                Response::METHOD_NOT_ALLOWED_STATUS_CODE,
                'Method not allowed. Only allowed methods: GET, POST.'
            );
        }

        try {
            $users = $this->user->getAll();
            $this->sendResponse(
                Response::SUCCESS_STATUS_CODE,
                'Users retrieved with success.',
                $users
            );
        } catch (Exception $exception) {
            $this->sendResponse(Response::INTERNAL_SERVER_ERROR_STATUS_CODE, $exception->getMessage());
        }
    }

    /**
     * Retrieve a specific user by it's id.
     * @return void
     */
    public function get(): void
    {
        if ($this->getRequestMethod() !== 'GET') {
            $this->sendResponse(
                Response::METHOD_NOT_ALLOWED_STATUS_CODE,
                'Method now allowed. Only allowed methods: GET.'
            );
        }

        $this->validateUserId($_GET);

        try {
            $user_id = (int) $_GET['user_id'];
            $user = $this->user->getById($user_id);

            if (empty($user)) {
                $this->sendResponse(Response::NOT_FOUND_STATUS_CODE, 'User not found.');
            }

            $this->sendResponse(Response::SUCCESS_STATUS_CODE, 'User retrieved with success.', $user);
        } catch (Exception $exception) {
            $this->sendResponse(Response::INTERNAL_SERVER_ERROR_STATUS_CODE, $exception->getMessage());
        }
    }

    /**
     * Update information for a specific user.
     * @return void
     */
    public function update(): void
    {
        if ($this->getRequestMethod() !== 'POST') {
            $this->sendResponse(
                Response::METHOD_NOT_ALLOWED_STATUS_CODE,
                'Method now allowed. Only allowed methods: POST'
            );
        }

        $this->validateUserId($_POST);

        try {
            $user_id = (int) $_POST['user_id'];
            $result = $this->user->update($user_id, $_POST);

            if (empty($result)) {
                $this->sendResponse(
                    Response::INTERNAL_SERVER_ERROR_STATUS_CODE,
                    'User could not be updated.'
                );
            }

            $this->sendResponse(Response::SUCCESS_STATUS_CODE, 'User updated with success.', $result);
        } catch (Exception $exception) {
            $this->sendResponse(Response::INTERNAL_SERVER_ERROR_STATUS_CODE, $exception->getMessage());
        }
    }

    /**
     * Delete a specific user by id.
     * @return void
     */
    public function delete(): void
    {
        if ($this->getRequestMethod() !== 'POST') {
            $this->sendResponse(
                Response::METHOD_NOT_ALLOWED_STATUS_CODE,
                'Method now allowed. Only allowed methods: POST'
            );
        }

        $this->validateUserId($_POST);

        try {
            $user_id = (int) $_POST['user_id'];
            $result = $this->user->delete($user_id);

            if (!$result) {
                $this->sendResponse(
                    Response::INTERNAL_SERVER_ERROR_STATUS_CODE,
                    'User could not be remove.'
                );
            }

            $this->sendResponse(Response::SUCCESS_STATUS_CODE, 'User deleted with success.');
        } catch (Exception $exception) {
            $this->sendResponse(Response::INTERNAL_SERVER_ERROR_STATUS_CODE, $exception->getMessage());
        }
    }

    /**
     * Verify if user id is valid.
     * @param array $request
     * @return bool
     */
    public function validateUserId(array $request): bool
    {
        if (!isset($request['user_id'])) {
            $this->sendResponse(Response::CLIENT_ERROR_STATUS_CODE, 'user_id is required.');
        }

        if (! (int) $request['user_id']) {
            $this->sendResponse(Response::CLIENT_ERROR_STATUS_CODE, 'user_id is invalid.');
        }

        return true;
    }

    public function sendResponse(string $status_code, string $message, array $data = []): void
    {
        $data = [
            'statusCode' => $status_code,
            'success' => $this->response->responseWithSuccess($status_code),
            'message' => $message,
            'data' => $data,
        ];
        $this->response->sendResponse($status_code, $data);
    }

    /**
     * @return string
     */
    public function getRequestMethod(): string
    {
        return (string) $_SERVER['REQUEST_METHOD'];
    }
}
